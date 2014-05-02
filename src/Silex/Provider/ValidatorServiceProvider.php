<?php namespace Deefour\Aide\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ValidatorServiceProvider implements ServiceProviderInterface {

  public function register(Application $app) {
    $app['validator'] = $app->share(function ($app) {
      $illuminateValidator = new \Illuminate\Validation\Factory(
        $app['translator']
      );

      return new \Deefour\Aide\Validation\IlluminateValidator(
        $illuminateValidator
      );
    });
  }

  public function boot(Application $app) { }
}
