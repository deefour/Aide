<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Persistence\Entity\EntityInterface;

abstract class Model extends Eloquent implements ModelInterface {

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $attributes, $flush = false) {
    if ($flush) {
      $this->flush();
    }

    $result = parent::setRawAttributes($attributes);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function setAttributes(array $attributes) {
    return $this->fromArray($attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function flush() {
    $this->attributes = [];
    $this->relations  = [];
    $this->original   = [];
  }

  /**
   * Attempt to derive the entity class based on the base name and namespace for
   * the class instance.
   *
   * @return \Deefour\Aide\Persistence\Entity\EntityInterface
   */
  public function toEntity() {
    $fullName = get_class($this);  // ie. \Eloquent\User
    list($namespace, $baseName) = array_pad(explode('\\', $fullName), -2, null);

    if ($this instanceof EntityInferface) {
      return clone $this;
    }

    $choices = array(
      "\\{$baseName}",       // \User
      "\\${baseName}Entity", // \UserEntity
      "\\${fullName}Entity", // \Eloquent\UserEntity
      "\\Entity${baseName}", // \Entity\User
    );

    foreach ($choices as $entityName) {
      if ( ! class_exists($entityName)) {
        continue;
      }

      $entityClass = new $entityName;

      if ($entityClass instanceof EntityInterface) {
        break;
      } else {
        $entityClass = null;
      }
    }

    if ( ! $entityClass) {
      throw new \Exception(
        sprintf(
          'Could not derive an entity class for the `%s` model. Looked for the
           following entity classes: `%s`',
           get_class($this),
           implode('`, `', $choices)
        )
      );
    }

    $entityClass->fromArray($this->getAttributes());
    $entityClass->exists = $this->exists;

    return $entityClass;
  }

  /**
   * {@inheritdoc}
   */
  public function newInstance($attributes = array(), $exists = false) {
    static::unguard();

    $model = new static((array) $attributes);

    static::reguard();

    $model->exists = $exists;

    return $model;
  }

}