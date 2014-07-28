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
      $config = $this->app['config'];

      if ( ! $config->has('policy.user')) {
        throw new \LogicException('A \'policy.user\' must be defined in the application config.');
      }

      $user   = $config->get('policy.user');
      $config = $config->get('policy');

      // The `user` option can be a Closure. If it is, get the return value
      if (is_callable($user)) {
        $user = call_user_func($user);
      }

      return new Policy($user, $config);
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
