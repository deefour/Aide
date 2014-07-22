<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Persistence\Entity\EntityInterface;



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



  public function __construct(ModelInterface $model = null, array $options = []) {
    $this->model   = $model;
    $this->options = $options;

    // derive the model class if not provided.
    if (is_null($this->model)) {
      $modelName = preg_replace('/Repository$/', '', get_called_class());

      $this->model = new $modelName;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function upsert(EntityInterface $entity, array $options = []) {
    if ( ! $entity->exists) {
      return $this->create($entity, $options);
    }

    return $this->update($entity, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function newInstance() {
    return $this->model->newInstance();
  }

}