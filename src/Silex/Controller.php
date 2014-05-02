<?php namespace Deefour\Aide\Silex;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Deefour\Aide\Persistence\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;



abstract class Controller {

  /**
   * The twig/rendering environment
   *
   * @protected
   * @var \Twig_Environment
   */
  protected $twig;

  /**
   * Silex/Symfony URL generation instance with all silex routes bound to it.
   *
   * @protected
   * @var \Symfony\Component\Routing\Generator\UrlGenerator
   */
  protected $urlGenerator;

  /**
   * Symfony HTTP Foundation session instance from the Silex service provider
   *
   * @protected
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * Symfony request object for the current page request
   *
   * @protected
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * SwiftMailer instance for sending email
   *
   * @protected
   * @var \Swift_Mailer
   */
  protected $mailer;

  /**
   * Monolog instance
   *
   * @protected
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Options for use within the class (ie. to customize attributes of the email
   * message)
   *
   * @protected
   * @var array
   */
  protected $options;

  /**
   * Filter methods to apply before the route action is executed. This allows
   * code to be set up within the constructor without actually executing
   * it until the route is going to be called
   *
   * @protected
   * @var array
   */
  protected $beforeFilters = [];

  /**
   * Application configuration
   *
   * @protected
   * @var array
   */
  protected $config;

  /**
   * The Illuminate validator instance-as-a-service
   *
   * @protected
   * @var \Deefour\Aide\Validation\ValidatorInterface
   */
  protected $validator;

  /**
   * The repository class responsible for `User` persistence/lookup
   *
   * @protected
   * @var \UserRepository
   */
  protected $userRepository;



  /**
   *
   *
   * @param  \Twig_Environment  $twig
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setTwig(\Twig_Environment $twig) {
    $this->twig = $twig;

    return $this;
  }

  /**
   *
   *
   * @param  \Symfony\Component\HttpFoundation\Request  $request
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setRequest(Request $request) {
    $this->request = $request;

    return $this;
  }

  /**
   *
   *
   * @param  \Swift_Mailer  $mailer
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setMailer(\Swift_Mailer $mailer) {
    $this->mailer = $mailer;

    return $this;
  }

  /**
   *
   *
   * @param  \Symfony\Component\Routing\Generator\UrlGenerator  $urlGenerator
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setUrlGenerator(UrlGenerator $urlGenerator) {
    $this->urlGenerator = $urlGenerator;

    return $this;
  }

  /**
   *
   *
   * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface  $session
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setSession(SessionInterface $session) {
    $this->session = $session;

    return $this;
  }

  /**
   *
   *
   * @param  \Psr\Log\LoggerInterface  $logger
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setLogger(LoggerInterface $logger) {
    $this->logger = $logger;

    return $this;
  }

  /**
   *
   *
   * @param  \Deefour\Aide\Persistence\Repository\RepositoryInterface  $session
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setUserRepository(RepositoryInterface $userRepository) {
    $this->userRepository = $userRepository;

    return $this;
  }

  /**
   *
   *
   * @param  \Illuminate\Database\Capsule\Manager  $capsule
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setDatabaseManager(Capsule $capsule) {
    $this->capsule = $capsule;

    return $this;
  }

  /**
   * Inject the application config into the controller
   *
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setConfig(array $config) {
    $this->config = $config;

    return $this;
  }

  /**
   *
   * @param  \Deefour\Aide\Validation\ValidatorInterface  $validator
   * @return \Deefour\Aide\Silex\Controller
   */
  public function setValidator(\Deefour\Aide\Validation\ValidatorInterface $validator) {
    $this->validator = $validator;

    return $this;
  }



  // Abstract Requirements
  // ---------------------------------------------------------------------------

  /**
   * Called from the \Deefour\Aide\Silex\Applicattion-provided controller builder,
   * after injecting all dependencies into the controller, this method will be
   * invoked for any final setup that needs to happen.
   *
   * In many cases this method will be left blank, but must exist within all controllers.
   *
   * @abstract
   */
  abstract public function boot();



  // Convenience methods
  // ---------------------------------------------------------------------------

  /**
   * Generate a `RedirectResponse`, redirecting the browser to the specified `$route`
   *
   * @protected
   * @param  string   $route
   * @param  array    $params  [optional]
   * @param  boolean  $mode  [optional]
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  protected function redirect($route, $params = [], $mode = UrlGeneratorInterface::ABSOLUTE_PATH) {
    if ( ! $this->urlGenerator instanceof UrlGenerator ) {
      throw new \Exception('The `$urlGenerator` property must be set on the `' . get_class($this) . '` to an instance of `\Symfony\Component\Routing\Generator\UrlGenerator` in order to call the `redirect()` shortcut.');
    }

    $destination = $this->urlGenerator->generate($route, $params, $mode);

    return new RedirectResponse($destination);
  }

  /**
   * Generate a `RedirectResponse`, redirecting the browser to the specified `$route`
   *
   * @protected
   * @param  string   $route
   * @param  array    $params  [optional]
   * @param  boolean  $mode  [optional]
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  protected function redirectNow($route, $params = [], $mode = UrlGeneratorInterface::ABSOLUTE_PATH) {
    $response = $this->redirect($route, $params, $mode);

    $response->send();
  }

  /**
   * Boolean check whether the user is logged into the app or not
   *
   * @protected
   * @return boolean
   */
  protected function authenticated() {
    if ( ! $this->session instanceof SessionInterface) {
      throw new \Exception('The `$session` property must be set on the `' . get_class($this) . '` to an instance of `\Symfony\Component\HttpFoundation\Session\SessionInterface` in order to call the `authenticated()` shortcut.');
    }

    return $this->session->has('user_id');
  }

  /**
   * The `User` instance for the currently-logged-in user, if present
   *
   * @protected
   * @return null|\User
   */
  protected function user() {
    if ( ! $this->authenticated()) {
      return null;
    }

    return $this->userRepository->find($this->session->get('user_id'));
  }

  /**
   * Renders the specified twig template.
   *
   * @protected
   * @param  string  $template
   * @param  array   $vars  [optional]
   * @return string
   */
  protected function render($template, array $vars = []) {
    $vars = array_merge(
      array(
        'current_user' => $this->user(),
      ),
      $vars
    );

    return $this->twig->render($template, $vars);
  }

  /**
   * Aborts the current request by sending a proper HTTP error.
   *
   * @protected
   * @param integer $statusCode The HTTP status code
   * @param string  $message    The status message
   * @param array   $headers    An array of HTTP headers
   */
  public function abort($statusCode, $message = '', array $headers = array()) {
    throw new HttpException($statusCode, $message, null, $headers);
  }

  /**
   * Generates an absolute application URL for the provided `$route` against the
   * ControllerCollection
   *
   * @protected
   * @param  string  $route
   * @param  array   $params  [optional]
   * @return string
   */
  protected function url($route, $params = []) {
    return $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
  }

  /**
   * Sets a flash message for the next request
   *
   * @param  string  $message
   * @param  string  $type  [optional]
   *
   * @return \Deefour\Aide\Silex\Controller
   */
  protected function flash($message, $type = 'info') {
    $this->session->getFlashBag()->add('flash', [ 'type' => $type, 'message' => $message ]);

    return $this;
  }

  /**
   * Sends a file. The intended use of this is strictly for file _downloads_
   *
   * @param  \SplFileInfo|string  $file
   * @param  string               $filename
   * @param  array                $headers
   *
   * @return BinaryFileResponse
   */
  public function sendFile($file, $filename, $headers = []) {
    if (is_string($file)) { // drop the contents into a temp file to pass to Symfony
      $fs      = new FileSystem;
      $tmpFile = tempnam(sys_get_temp_dir(), 'test');

      $fs->dumpFile($tmpFile, $file);

      $file = $tmpFile;
    }

    $response = new BinaryFileResponse($file, 200, $headers);

    $response->trustXSendfileTypeHeader();
    $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        $filename,
        iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
    );

    return $response;
  }

}