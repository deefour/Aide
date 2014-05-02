<?php namespace Deefour\Aide\Persistence\Repository\Factory;

abstract class AbstractFactory implements FactoryInterface {

  /**
   * Configurable options for the factory
   *
   * @var array
   */
  protected static $options = [];



  /**
   * Setter for options to be used on all repositories of the same type
   *
   * @param  array  $options
   */
  public function setOptions(array $options) {
    static::$options = array_replace_recursive(static::$options, $options);
  }

  /**
   * Splits the fully qualified name of the entity class into it's namespace and
   * base name, returning an array (to be used as a list of variables by the caller)
   * of these parts along with the full name.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return array
   */
  protected static function parseClassName(\Deefour\Aide\Persistence\Entity\EntityInterface $entity) {
    $fullClassName = get_class($entity);
    $namespace     = join('\\', array_slice(explode('\\', $fullClassName), 0, -1));
    $className     = join('', array_slice(explode('\\', $fullClassName), -1));

    return [ $fullClassName, $namespace, $className ];
  }

  /**
   * Magic call method to allow the factory to generate repository instances from
   * an instance of the factory class itself, calling out to the static methods
   * without throwing warnings.
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public function __call($method, $parameters) {
    return call_user_func_array(array($this, $method), $parameters);
  }

}