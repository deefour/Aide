<?php namespace Deefour\Aide\Persistence\Repository\Eloquent;

use Deefour\Aide\Persistence\Repository\AbstractRepository;
use Deefour\Aide\Persistence\Repository\RepositoryInterface;
use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Persistence\Entity\EntityInterface;
use \Deefour\Aide\Persistence\Model\Eloquent\Model;



abstract class Repository extends AbstractRepository implements RepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function create(EntityInterface $entity, array $options = []) {
    $model = $entity;

    if ( ! $entity instanceof ModelInterface) {
      $model = $this->model->newInstance($entity->getAttributes());
    }

    if ( ! $model->save($options)) {
      return false;
    }

    $entity->setAttributes($model->getAttributes());

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function update(EntityInterface $entity, array $options = []) {
    $model = $entity;

    if ( ! $entity instanceof ModelInterface) {
      $model = $this->model->newInstance($entity->getAttributes(), true);
    }

    if ( ! $model->save($options)) {
      return false;
    }

    $entity->setAttributes($model->getAttributes());

    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(EntityInterface $entity) {
    if (is_null($entity->id)) {
      return true;
    }

    return $this->query()->where('id', $entity->id)->delete();
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
