<?php namespace Deefour\Aide\Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RepositoryServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   *
   * Configures the repository builder service. This builds a repository class
   * that will interact with configured storage backend, specific to any instance
   * of \Deefour\Aide\Persistence\Entity\EntityInterface passed in.
   */
  public function register(Application $app) {
    $app['repository_factory'] = $app->share(function($app) {
      if ($app['repository.options']['engine'] === 'csv') {
        $factory = new \Deefour\Aide\Persistence\Repository\Factory\CsvFactory;

        $factory->setOptions($app['repository.options']['csv']);

        return $factory;
      } else {
        return new \Deefour\Aide\Persistence\Repository\Factory\EloquentFactory;
      }
    });
  }

  public function boot(Application $app) { }

}
