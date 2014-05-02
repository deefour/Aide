<?php namespace Deefour\Aide\Persistence\Entity;

interface EntityInterface {

  /**
   * List of rules to use in the validation abstraction layer to ensure all required
   * information has been provided in the expected format.
   *
   * @param  array  $context  [optional]
   * @return array
   */
  public function validations(array $context = []);

  /**
   * Non-destructive `fromArray` implementation, setting specified attributes
   * without first flushing all existing values.
   *
   * @return void
   */
  public function setAttributes(array $attributes);

  /**
   * This method is defined by `\Illuminate\Database\Eloquent\Model`, so this
   * method guarantees compatibility with `illuminate/database`.
   *
   * Returns a collection of all attributes on the model
   *
   * @return array
   */
  public function getAttributes();

  /**
   * Same functionality as `setAttributes`, but first provides the option (disabled
   * by default) to flush out any previously set values on the entity.
   *
   * @see setAttributes
   * @see flush
   * @param  array    $attributes
   * @param  boolean  $flush[optional]
   *
   * @return void
   */
  public function fromArray(array $attributes, $flush);

  /**
   * Converts the entities list of attributes (public properties) into an easily-
   * consumable set of key/value pairs.
   *
   * @return array
   */
  public function toArray();

  /**
   * Clears any previously set values on all attributes for the entity.
   *
   * @return void
   */
  public function flush();

}