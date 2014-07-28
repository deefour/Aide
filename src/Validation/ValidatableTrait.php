<?php namespace Deefour\Aide\Validation;

trait ValidatableTrait {

  /**
   * Custom error message templates for this entity
   *
   * @protected
   * @var array
   */
  protected $messageTemplates = [];

  /**
   * {@inheritdoc}
   *
   * @throws BadMethodCallException if no `validations` method has been provided
   * on the inheriting class
   */
  public function validations(array $context = []) {
    throw new \BadMethodCallException('A `validations` method has not been defined for this classs');
  }

  /**
   * A list of error message templates specific to this entity.
   *
   * @return array
   */
  public function getMessageTemplates() {
    return $this->messageTemplates;
  }

}
