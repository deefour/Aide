<?php namespace Deefour\Aide\Persistence\Repository\Csv;

use Illuminate\Support\ServiceProvider;
use Deefour\Aide\Persistence\Repository\Factory\Csv\Factory;

class RepositoryServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;



  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->bindShared('aide.repository', function() {
      return new Factory(
        $this->app['config']->get('repository.csv')
      );
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [ 'aide.repository' ];
  }

}