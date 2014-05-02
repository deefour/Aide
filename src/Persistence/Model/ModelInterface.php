<?php namespace Deefour\Aide\Persistence\Model;

interface ModelInterface {

  /**
   * Converts the model's whitelisted/allowed attributes into an array of keys
   * and values. This can typically be imported directly into an entity's
   * `fromArray` method
   *
   * @return array
   */
  public function toArray();

  /**
   * Accepts an array of attributes, mapping the keys of the array directly onto
   * white-listed/mass-assignable attributes on the model.
   *
   * @param  array  $attributes
   * @return void
   */
  public function setAttributes(array $attributes);

}