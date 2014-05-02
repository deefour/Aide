<?php namespace Deefour\Aide\Persistence\Repository\Factory;

use \Deefour\Aide\Persistence\Entity\EntityInterface;
use \Deefour\Aide\Persistence\Model\Eloquent\Model;



abstract class AbstractFactory implements FactoryInterface {

  /**
   * Configurable options for the factory
   *
   * @var array
   */
  protected static $options = [];

  /**
   * The name of the storage driver
   *
   * @protected
   * @var string
   */
  protected static $driver;



  /**
   * {@inheritdoc}
   */
  public static function create(EntityInterface $entity, array $options = []) {
    $options    = array_merge(static::$options, $options);
    $model      = static::deriveModelName($entity);
    $repository = static::deriveRepositoryName($entity);

    return new $repository(new $model, $options);
  }

  /**
   * Setter for options to be used on all repositories of the same type
   *
   * @static
   * @param  array  $options
   */
  public static function setOptions(array $options) {
    static::$options = array_replace_recursive(static::$options, $options);
  }



  /**
   * Splits the fully qualified name of the entity class into it's namespace and
   * base name, returning an array (to be used as a list of variables by the caller)
   * of these parts along with the full name.
   *
   * @static
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return array
   */
  protected static function parseClassName(EntityInterface $entity) {
    $fullClassName = get_class($entity);
    $namespace     = join('\\', array_slice(explode('\\', $fullClassName), 0, -1));
    $className     = join('', array_slice(explode('\\', $fullClassName), -1));

    return [ $fullClassName, $namespace, $className ];
  }

  /**
   * Attempts to derive the model class based on the storage driver of the type
   * of repository being created.
   *
   * @static
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return string
   */
  protected static function deriveModelName(EntityInterface $entity) {
    $driver = static::$driver;

    if ($entity instanceof Model) {
      return get_class($entity);
    } else {
      list($fullClassName, $namespace, $classBaseName) = static::parseClassName($entity);

      $modelClass = "\\${driver}\\${classBaseName}";

      if (class_exists($modelClass)) {
        return $modelClass;
      }
    }

    throw new \Exception(sprintf(
      'An instance of `%s` could not be derived from the `%s` entity class passed to the `%s::create()` method',
      "\\Deefour\\Aide\\Persistence\\Model\\${driver}\\Model",
      get_class($entity),
      get_class($this)
    ));
  }

  /**
   * Attempts to derive the repository class for the current storage driver based
   * on the entity and model passed in.
   *
   * @static
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return string
   */
  protected static function deriveRepositoryName(EntityInterface $entity) {
    $driver = static::$driver;

    list($fullClassName, $namespace, $classBaseName) = static::parseClassName($entity);
    $className = sprintf('%s\\%sRepository', $namespace, $classBaseName);

    if (class_exists($className)) {
      return $className;
    }

    // the repository class could not be found within the global namespace. Let's look
    // into the namespace matching the persistence driver
    $driverClassName = "\\${driver}${className}";

    if ( ! class_exists($driverClassName)) {
      throw new \Exception("`${driver}Factory::create` could not instantiate an instance of `${className}` or `${driverClassName}` for the `${fullClassName}` entity");
    }

    return $driverClassName;
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