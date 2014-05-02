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
   */
  public function setEntity(\Deefour\Aide\Persistence\Entity\EntityInterface $entity);

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
  public function isValid();

  /**
   * Common API accessor for the errors (if any) set on the validation library,
   * cast to a human-readable array.
   *
   * @return array
   */
  public function errors();

}