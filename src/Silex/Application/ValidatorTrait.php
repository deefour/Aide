<?php namespace Deefour\Aide\Silex\Application;

/**
 * Trait for the main Application, providing a shortcut create an validation class
 * instance based on a provided entity
 */
trait ValidatorTrait {

  /**
   * Creates a new validation class instance based on the provided entity
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return \Deefour\Aide\Validation\ValidationInterface
   */
  public function validator(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    $this['validator']->setEntity($entity);

    return $this['validator'];
  }

}
