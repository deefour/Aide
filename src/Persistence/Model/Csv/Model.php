<?php namespace Deefour\Aide\Persistence\Model\Csv;

use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Validation\ValidatableInterface;
use Deefour\Aide\Validation\ValidatableTrait;

abstract class Model implements \ArrayAccess, ModelInterface, ValidatableInterface {

  use ValidatableTrait;



  /**
   * List of columns that should be considered attributes for the data store
   *
   * @var array
   */
  protected $columns = [ 'id' ];

  /**
   * Key/value pairs of all data associated with this model
   *
   * @var array
   */
  protected $attributes = [];

  /**
   * The column/attribute used to uniquely identify a record
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * Whether or not the model has been persisted to storage
   *
   * @var boolean
   */
  public $exists = false;



  public function __construct(array $attributes = []) {
    $this->fromArray($attributes);

    if ($this->getId()) {
      $this->exists = true;
    } else {
      $this->setId();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function newInstance($attributes = array(), $exists = false) {
    $model = new static($attributes);

    $model->exists = $exists;

    return $model;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    return array_merge([ 'id' => $this->getId() ], $this->attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $attributes) {
    foreach ($attributes as $column => $value) {
      $this->setAttribute($column, $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setAttributes(array $attributes) {
    $this->fromArray($attributes);
  }

  /**
   * Accessor for the column value that uniquely identifies this model
   *
   * @return string|int
   */
  public function getId() {
    return $this->getAttribute($this->primaryKey);
  }

  /**
   * Generates a random string of configurable length (up to 32 chars or as low
   * as 3 chars) to uniquely identify the record.
   *
   * @param  int  $length
   * @return string
   */
  protected function setId($length = 7) {
    $length = min(32, max(7, $length));

    if ( ! $this->getId()) {
      $this->attributes[$this->primaryKey] = substr(md5(rand()), 0, $length);
    }

    return $this->getId();
  }

  /**
   * Accessor for a specific column value set on the model instance
   *
   * @param  string  $key
   * @return mixed
   */
  public function getAttribute($key) {
    if ($this->offsetExists($key)) {
      return $this->attributes[$key];
    }

    return null;
  }

  /**
   * Setter for a specific column (based on a defined whitelist) on the model instance
   *
   * @param  string  $key
   * @param  mixed   $value
   * @return boolean whether or not it was saved (TRUE if the column is valid/white-listed)
   */
  public function setAttribute($key, $value) {
    if ( ! $this->isColumn($key)) {
      return false;
    }

    $this->attributes[$key] = $value;

    return $this->isColumn($key);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->attributes);
  }

  /**
   * {@inheritdoc}
   *
   * @throws InvalidArgumentException if the `$offset` is not a valid attribute
   */
  public function offsetGet($offset) {
    if ( ! $this->offsetExists($offset)) {
      throw new \InvalidArgumentException("Specified column `$offset` is not a valid attribute");
    }

    return $this->getAttribute($offset);
  }

  /**
   * {@inheritdoc}
   *
   * @throws InvalidArgumentException if the `$offset` is not a valid attribute
   */
  public function offsetSet($offset, $value) {
    if ( ! in_array($offset, $this->columns())) {
      throw new \InvalidArgumentException("Specified column `$offset` is not a valid attribute");
    }

    $this->setAttribute($offset, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($offset) {
    $this->setAttribute($offset, null);
  }

  public function isColumn($column) {
    return in_array($column, $this->columns());
  }

  /**
   * Dynamically retrieve attributes on the model.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key) {
    return $this->getAttribute($key);
  }

  /**
   * Dynamically set attributes on the model.
   *
   * @param  string  $key
   * @param  mixed   $value
   * @return void
   */
  public function __set($key, $value) {
    if ($this->setAttribute($key, $value)) {
      return;
    }

    $this->$key = $value;
  }



  /**
   * A list of columns
   *
   * NOTE: ORDER HERE IS IMPORTANT. MODIFYING THE ORDER COLUMNS APPEAR IN THE
   * INHERITED ARRAY CAN CAUSE ISSUES IN EXISTING APPLICATIONS. IT IS ALWAYS
   * RECOMMENDED THAT NEW ATTRIBUTES BE APPENDED TO THE _END_ OF THE ARRAY
   *
   * @return array
   */
  abstract public function columns();

}