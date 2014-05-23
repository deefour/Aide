<?php namespace Deefour\Aide\Authorization;

use Illuminate\Support\ServiceProvider;



class PolicyServiceProvider extends ServiceProvider {

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
    $this->app->bindShared('aide.policy', function() {
      return new Policy($this->app['config']->get('policy'));
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [ 'aide.policy' ];
  }

}