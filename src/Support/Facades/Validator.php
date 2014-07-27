<?php namespace Deefour\Aide\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Deefour\Aide\Validation\ValidatorServiceProvider
 */
class Validator extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'aide.validator'; }

}