<?php namespace Deefour\Aide\Persistence\Repository;

abstract class AbstractRepository implements RepositoryInterface {

  /**
   * Configurable options for the repository class
   *
   * @var array
   */
  protected $options;

  /**
   * Model which the repository will work around (all conversions between entities
   * and models for this class instance will be based around models of this type)
   *
   * @var Deefour\Aide\Persistence\Model\ModelInterface
   */
  protected $model;



  public function __construct(\Deefour\Aide\Persistence\Model\ModelInterface $model, array $options = []) {
    $this->model   = $model;
    $this->options = $options;
  }

  /**
   * {@inheritdoc}
   */
  public function upsert(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    if ( ! $entity->exists) {
      return $this->create($entity);
    }

    return $this->update($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function newInstance() {
    return $this->model->newInstance();
  }

}