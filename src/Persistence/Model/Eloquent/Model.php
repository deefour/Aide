<?php namespace Deefour\Aide\Persistence\Model\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Deefour\Aide\Persistence\Model\ModelInterface;

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

}