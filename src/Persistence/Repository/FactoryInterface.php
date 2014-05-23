<?php namespace Deefour\Aide\Persistence\Repository;

use \Deefour\Aide\Persistence\Entity\EntityInterface;



interface FactoryInterface {

  /**
   * Static passthru for the create method on this same class
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                             $options [optional]
   * @return \Deefour\Aide\Persistence\Repository\RepositoryInterface
   */
  public static function make(EntityInterface $entity, array $options = []);

  /**
   * Builds a repository class to accept entities of the specified type and perform
   * actions on created/modified model representations of the raw data found in
   * those entities.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                             $options [optional]
   * @return \Deefour\Aide\Persistence\Repository\RepositoryInterface
   */
  public function create(EntityInterface $entity, array $options = []);

}