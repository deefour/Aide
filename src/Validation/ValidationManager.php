<?php namespace Deefour\Aide\Validation;

use Illuminate\Support\Manager;
use Illuminate\Validation\Factory as IlluminateFactory;

class ValidationManager extends Manager {

  /**
   * Create an instance of the Illuminate validation driver.
   *
   * @return \Deefour\Aide\Validation\AbstractValidator
   */
  protected function createIlluminateDriver() {
    $factory = new IlluminateFactory($this->app['translator'], $this->app);

    $factory->setPresenceVerifier($this->app['validation.presence']);

    return new IlluminateValidator($factory);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultDriver() {
    return $this->app['config']['validation.driver'];
  }

}
