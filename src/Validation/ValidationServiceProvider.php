<?php namespace Deefour\Aide\Validation;

use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {

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
    $this->app->bindShared('aide.validator', function($app) {
      return new ValidationManager($app);
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return [ 'aide.validator' ];
  }

}