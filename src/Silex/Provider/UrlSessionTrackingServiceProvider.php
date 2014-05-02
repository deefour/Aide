<?php namespace Deefour\Aide\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Deefour\Aide\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class UrlSessionTrackingServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   *
   * Overrides the default Silex-provided url generate with one that will inject
   * a special session id querystring parameter into _all_ urls (including those
   * generated within twig templates) created through the app
   *
   * @param  \Silex\Application  $app
   */
  public function register(Application $app) {
    $app['url_generator'] = $app->share(function ($app) {
      $app->flush();

      return new UrlGenerator($app['routes'], $app['request_context'], null, $app['session']);
    });
  }

  public function boot(Application $app) {
    // set up the middleware with short-circuit
    $app->before(function(Request $request) use ($app) {
      return $this->enforceValidSessionId($app, $request);
    });
  }

  // middleware
  public function enforceValidSessionId(Application $app, Request $request) { // make sure they registered
    $sessionID = $request->get($app['session.options']['name']);

    if ( ! empty($sessionID)) {
      $app['session']->setId($sessionID);
      $app['session']->start();
    }

    if (empty($sessionID) or ! $app['session']->has('token')) {
      $app['session']->invalidate();
      $app['session']->set('token', substr(md5(mt_rand()), 0, 15));

      return $app->redirect($app['url_generator']->generate('home'));
    }
  }
}
