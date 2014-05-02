<?php namespace Deefour\Aide\Silex\Provider;

/**
 * Provider for illuminate/database Laravel component
 *
 * @link https://gist.github.com/ziadoz/7326872
 */

use Silex\Application;
use Silex\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Cache\CacheManager;

class CapsuleServiceProvider implements ServiceProviderInterface {

  /**
   * Register the Capsule service.
   * See: http://stackoverflow.com/questions/17105829/using-eloquent-orm-from-laravel-4-outside-of-laravel
   *
   * @param Silex\Application $app
   **/
  public function register(Application $app) {
    $app['capsule.connection_defaults'] = array(
      'driver'    => 'mysql',
      'host'      => 'localhost',
      'database'  => null,
      'username'  => 'root',
      'password'  => null,
      'charset'   => 'utf8',
      'collation' => 'utf8_unicode_ci',
      'prefix'    => null,
    );

    $app['capsule.global']   = true;
    $app['capsule.eloquent'] = true;

    $app['capsule.container'] = $app->share(function() {
      return new Container;
    });

    $app['capsule.dispatcher'] = $app->share(function() use($app) {
      return new Dispatcher($app['capsule.container']);
    });

    if (class_exists('Illuminate\Cache\CacheManager')) {
      $app['capsule.cache_manager'] = $app->share(function() use($app) {
        return new CacheManager($app['capsule.container']);
      });
    }

    $app['capsule'] = $app->share(function($app) {
      $capsule = new Capsule($app['capsule.container']);
      $capsule->setEventDispatcher($app['capsule.dispatcher']);

      if (isset($app['capsule.cache_manager']) && isset($app['capsule.cache'])) {
        $capsule->setCacheManager($app['capsule.cache_manager']);

        foreach ($app['capsule.cache'] as $key => $value) {
          $app['capsule.container']->offsetGet('config')->offsetSet('cache.' . $key, $value);
        }
      }

      if ($app['capsule.global']) {
        $capsule->setAsGlobal();
      }

      if ($app['capsule.eloquent']) {
        $capsule->bootEloquent();
      }

      if (! isset($app['capsule.connections'])) {
        $app['capsule.connections'] = array(
          'default' => (isset($app['capsule.connection']) ? $app['capsule.connection'] : array()),
        );
      }

      foreach ($app['capsule.connections'] as $connection => $options) {
        $capsule->addConnection(array_replace($app['capsule.connection_defaults'], $options), $connection);
      }

      if ( ! $app['debug']) {
        $capsule->connection()->disableQueryLog();
      }

      return $capsule;
    });
  }

  /**
   * Boot the Capsule service.
   *
   * @param Silex\Application $app;
   **/
  public function boot(Application $app) {
    if ( ! $app['capsule.eloquent']) {
      return;
    }

    $app['capsule'];
  }
}