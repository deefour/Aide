<?php namespace Deefour\Aide\Silex;

/**
 * Extension of the base Silex application class, allowing for customization.
 *
 * This base class is used in the bootstrap process to launch the app, *not*
 * Silex itself.
 */
class Application extends \Silex\Application {

  // Silex-provided traits
  use \Silex\Application\TwigTrait;
  use \Silex\Application\UrlGeneratorTrait;
  use \Silex\Application\MonologTrait;
  use \Silex\Application\SwiftmailerTrait;

  // Deefour-namespaced traits
  use \Deefour\Aide\Silex\Application\RepositoryTrait;
  use \Deefour\Aide\Silex\Application\ValidatorTrait;



  /**
   * Override constructor for the application, extending the main Silex application
   *
   * Configures the controller builder service, injecting common dependencies into
   * requested controllers at runtime.
   *
   * @param  array  $values  [optional]
   */
  public function __construct(array $values = []) {
    parent::__construct($values);

    $this['controller_builder'] = $this->protect(function($controller) {
      $controller->setTwig($this['twig'])
                 ->setRequest($this['request'])
                 ->setMailer($this['mailer'])
                 ->setUrlGenerator($this['url_generator'])
                 ->setSession($this['session'])
                 ->setLogger($this['monolog'])
                 ->setUserRepository($this->repository(new \User))
                 ->setDatabaseManager($this['capsule'])
                 ->setConfig($this['config'])
                 ->setValidator($this['validator']);

      $controller->boot();

      return $controller;
    });
  }

}
