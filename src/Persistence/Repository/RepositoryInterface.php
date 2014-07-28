<?php namespace Deefour\Aide\Persistence\Repository;

use \Deefour\Aide\Persistence\Entity\EntityInterface;

interface RepositoryInterface {

  /**
   * Persists a new record into the storage backend.
   *
   * @param  Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                            $options
   * @return Deefour\Aide\Persistence\Model\ModelInterface
   */
  public function create(EntityInterface $entity, array $options = []);

  /**
   * Persists an _existing_ record into the storage backend.
   *
   * @param  Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                            $options
   * @return Deefour\Aide\Persistence\Model\ModelInterface
   */
  public function update(EntityInterface $entity, array $options = []);

  /**
   * Convenience method; creates non-existent entities and updates existing ones.
   * This is just a passthru to the create/update methods on the repository
   *
   * @param  Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  array                                            $options
   * @return boolean
   */
  public function upsert(EntityInterface $entity, array $options = []);

  /**
   * Removes a record from the storage backend if it exists; removes it from
   * the in-memory store if present
   *
   * @param  Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return boolean
   */
  public function delete(EntityInterface $entity);

  /**
   * Attempts to find an existing record in the storage backend, converting it
   * to a generic entity for consumption
   *
   * @param  mixed  $id
   * @return \Deefour\Aide\Persistence\Entity\EntityInterface|null $entity
   */
  public function find($id);

  /**
   * Retrieves entities for all records in the storage backend.
   *
   * @return array
   */
  public function all();

  /**
   * Creates a new instance of the model this repository class deals with
   *
   * @return \Deefour\Aide\Persistence\Model\ModelInterface $model
   */
  public function newInstance();

}
