<?php namespace Deefour\Aide\Validation;

/**
 * Contract for validation library abstractions to follow for validation and
 * error retrieval/display.
 */
interface ValidatorInterface {

  /**
   * Sets the entity for the validation class. The entity contains a list of
   * rules to follow within a public `validations` method
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                             $context
   */
  public function make(\Deefour\Aide\Validation\ValidatableInterface $entity, array $context = []);

  /**
   * Accessor for the previously-set entity on the validation class instance
   *
   * @return \Deefour\Aide\Persistence\Entity\EntityInterface
   */
  public function getEntity();

  /**
   * Performs validation checks using the rules set on/provided by the entity
   * against the validation library
   *
   * @return boolean
   */
  public function validate();

  /**
   * Common API accessor for the errors (if any) set on the validation library,
   * cast to a human-readable array.
   *
   * @return array
   */
  public function getErrors();

}