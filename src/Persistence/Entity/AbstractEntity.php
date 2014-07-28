<?php namespace Deefour\Aide\Persistence\Entity;

use Deefour\Aide\Validation\ValidatableInterface;
use Deefour\Aide\Validation\ValidatableTrait;

abstract class AbstractEntity implements EntityInterface, ValidatableInterface {

  use ValidatableTrait;



  /**
   * By default all entities have the id attribute available as the unique
   * identifier for the entity
   *
   * @var string|int
   */
  public $id;

  /**
   * Whether or not this entity's data is related ot a persisted record in the
   * data store
   *
   * @var boolean
   */
  public $exists = false;

  /**
   * Whitelist of protected properties that magic __get and __set should allow
   * read/write access to
   *
   * @protected
   * @var array
   */
  protected $nonAttributeProperties = [ 'exists' ];



  public function __construct($data = null) {
    if (is_array($data)) {
      $this->setAttributes($data);
    }
  }



  /**
   * {@inheritdoc}
   */
  public function setAttributes(array $attributes) {
    $whitelist = $this->attributeList();

    foreach ($attributes as $attribute => $value) {
      if (in_array($attribute, $whitelist)) {
        $this->$attribute = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $attributes, $flush = false) {
    if ($flush) {
      $this->flush();
    }

    $this->setAttributes($attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    $attributes = $this->attributeList();
    $data       = [];

    foreach ($attributes as $attribute) {
      $data[$attribute] = $this->$attribute;
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function attributesToArray() {
    return $this->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getAttributes() {
    return $this->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function flush() {
    $attributes = $this->attributeList();

    foreach ($attributes as $attribute) {
      $this->$attribute = null;
    }
  }



  /**
   * Accessor for local protected property, providing easy extension point for
   * inheriting classes.
   *
   * @return array
   */
  protected function getNonAttributeProperties() {
    return $this->nonAttributeProperties;
  }

  /**
   * Builds a whitelist of attributes for use in the setters/getters on the entity.
   * Currently this simply grabs all public class properties, filtering out
   * any that exist in the special list of non-attribute properties.
   *
   * @return array
   */
  protected function attributeList() {
    $attributes = [];
    $reflection = new \ReflectionClass($this);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

    foreach ($properties as $property) {
      if (in_array($property->getName(), $this->getNonAttributeProperties())) {
        continue;
      }

      $attributes[] = preg_replace('/\*/', '', $property->getName());
    }

    return $attributes;
  }

}