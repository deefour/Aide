<?php namespace Deefour\Aide\Persistence\Repository\Eloquent;

use Illuminate\Support\ServiceProvider;
use Deefour\Aide\Persistence\Repository\Factory\EloquentFactory as Factory;



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
    $this->app->bindShared('repository', function() {
      return new Factory;
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [ 'repository' ];
  }

}