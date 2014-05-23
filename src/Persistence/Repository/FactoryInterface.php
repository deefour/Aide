<?php namespace Deefour\Aide\Persistence\Repository;

use \Deefour\Aide\Persistence\Entity\EntityInterface;



interface FactoryInterface {

  /**
   * Builds a repository class to accept entities of the specified type and perform
   * actions on created/modified model representations of the raw data found in
   * those entities.
   *
   * @static
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return \Deefour\Aide\Persistence\Repository\RepositoryInterface
   */
  public static function create(EntityInterface $entity);

}