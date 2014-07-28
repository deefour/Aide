<?php namespace Deefour\Aide\Validation;

interface ValidatableInterface {

  /**
   * List of rules to use in the validation abstraction layer to ensure all required
   * information has been provided in the expected format.
   *
   * @param  array  $context  [optional]
   * @return array
   */
  public function validations(array $context = []);

  /**
   * A list of error message templates specific to this entity.
   *
   * @return array
   */
  public function getMessageTemplates();

}
