<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\Persistence\Model\ModelInterface;

abstract class EloquentRepository extends AbstractRepository implements RepositoryInterface {

  public function __construct(\Deefour\Aide\Persistence\Model\Eloquent\Model $model, array $options = []) {
    parent::__construct($model, $options);
  }



  /**
   * {@inheritdoc}
   */
  public function create(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    $model = $entity;

    if ( ! $entity instanceof ModelInterface) {
      $model = $this->model->newInstance($entity->getAttributes());
    }

    if ( ! $model->save()) {
      return false;
    }

    $entity->setAttributes($model->getAttributes());

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function update(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    $model = $entity;

    if ( ! $entity instanceof ModelInterface) {
      $model = $this->model->newInstance($entity->getAttributes(), true);
    }

    if ( ! $model->save()) {
      return false;
    }

    $entity->setAttributes($model->getAttributes());

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    if (is_null($entity->id)) {
      return true;
    }

    return $this->query()->where('id', $id)->delete();
  }

  /**
   * {@inheritdoc}
   */
  public function find($id) {
    return $this->query()->find($id);
  }

  /**
   * {@inheritdoc}
   */
  public function all() {
    return $this->query()->get();
  }



  /**
   * Convenience method to start a new query based on the model type associated
   * with this repository instance
   *
   * @return Illuminate\Database\Query\Builder
   */
  protected function query() {
    return $this->model->newQuery();
  }

}
