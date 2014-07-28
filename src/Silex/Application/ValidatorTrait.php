<?php namespace Deefour\Aide\Silex\Application;

use Deefour\Aide\Persistence\Entity\EntityInterface;

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
  public function validator(EntityInterface $entity) {
    $this['validator']->setEntity($entity);

    return $this['validator'];
  }

}
