<?php namespace Deefour\Aide\Silex\Application;

/**
 * Trait for the main Application, providing shortcuts to work with the currently
 * configured storage engine through Silex's service locator.
 */
trait RepositoryTrait {

  /**
   * Shortcut to build a new repository for the storage engine configured into
   * the Silex service locator.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return \Deefour\Aide\Persistence\Repository\RepositoryInterface
   */
  public function repository(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    return $this['repository_factory']->create($entity);
  }

}
