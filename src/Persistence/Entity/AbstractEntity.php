<?php namespace Deefour\Aide\Persistence\Entity;

abstract class AbstractEntity implements EntityInterface {

  /**
   * By default all entities have the id attribute available as the unique
   * identifier for the entity
   *
   * @var string|int
   */
  public $id;

  /**
   * Custom error message templates for this entity
   *
   * @protected
   * @var array
   */
  protected $messageTemplates = [];



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
   * {@inheritdoc}
   *
   * @throws BadMethodCallException if no `validations` method has been provided
   * on the inheriting class
   */
  public function validations(array $context = []) {
    throw new \BadMethodCallException('A `validations` method has not been defined for this classs');
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageTemplates() {
    return $this->messageTemplates;
  }



  /**
   * Builds a whitelist of attributes for use in the setters/getters on the entity.
   * Currently this simply grabs all public and protected class properties.
   *
   * @return array
   */
  protected function attributeList() {
    $attributes = [];
    $reflection = new \ReflectionClass($this);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

    foreach ($properties as $property) {
      $attributes[] = preg_replace('/\*/', '', $property->getName());
    }

    return $attributes;
  }



  /**
   * Magic getter, providing property-level access to protected properties
   *
   * @param  mixed  $var
   * @return mixed
   */
  public function __get($var) {
    if (property_exists($this, $var)) {
      $reflection = new \ReflectionProperty($this, $var);

      if ($reflection->isProtected()) {
        return $this->$var;
      }
    }

    return null;
  }

}