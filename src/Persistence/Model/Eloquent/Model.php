<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Persistence\Entity\EntityInterface;
use Deefour\Aide\Validation\ValidatableInterface;



abstract class Model extends Eloquent implements EntityInterface, ModelInterface, ValidatableInterface {

  /**
   * Custom error message templates for this entity
   *
   * @protected
   * @var array
   */
  protected $messageTemplates = [];

  /**
   * Keep the mass assignment protection out of the model layer. Instead, expect
   * the developer to protect from mass assignment from within the controller.
   *
   * {@inheritdoc}
   */
  protected $guarded = [];



  /**
   * {@inheritdoc}
   */
  public function fromArray(array $attributes, $flush = false) {
    if ($flush) {
      $this->flush();
    }

    $result = $this->fill($attributes);

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function setAttributes(array $attributes) {
    return $this->fill($attributes);
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
    $entityClass = null;
    $fullName    = get_class($this);  // ie. \Eloquent\User

    list($namespace, $baseName) = array_pad(explode('\\', $fullName), -2, null);

    if ($this instanceof EntityInterface) {
      $entityClass = $this;
    } else {
      $choices = array(
        "\\{$baseName}",       // \User
        "\\${baseName}Entity", // \UserEntity
        "\\${fullName}Entity", // \Eloquent\UserEntity
        "\\Entity${baseName}", // \Entity\User
      );

      foreach ($choices as $entityName) {
        if ( ! class_exists($entityName) or  ! (new $entityName instanceof EntityInterface)) {
          continue;
        }

        $entityClass = new $entityName;
        break;
      }
    }

    if ( ! $entityClass) {
      throw new \LogicException(
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

  /**
   * {@inheritdoc}
   *
   * @throws BadMethodCallException if no `validations` method has been provided
   * on the inheriting class
   */
  public function validations(array $context = []) {
    throw new \BadMethodCallException('A `validations` method has not been defined for this class');
  }

  /**
   * A list of error message templates specific to this entity.
   *
   * @return array
   */
  public function getMessageTemplates() {
    return $this->messageTemplates;
  }

}