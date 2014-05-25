<?php namespace Deefour\Aide\Persistence\Repository;

use \Deefour\Aide\Persistence\Entity\EntityInterface;
use \Deefour\Aide\Persistence\Model\Eloquent\Model;



abstract class AbstractFactory implements FactoryInterface {

  /**
   * Configurable options for the factory
   *
   * @var array
   */
  protected $options = [];

  /**
   * The name of the storage driver
   *
   * @protected
   * @var string
   */
  protected $driver;



  /**
   * {@inheritdoc}
   */
  public static function make(EntityInterface $entity, array $options = []) {
    return (new static)->create($entity, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function create(EntityInterface $entity, array $options = []) {
    $this->setOptions($options);

    $model      = $this->deriveModelName($entity);
    $repository = $this->deriveRepositoryName($entity);

    return new $repository(new $model, $this->options);
  }

  /**
   * Setter for options to be used on all repositories of the same type
   *
   * @param  array  $options
   */
  public function setOptions(array $options) {
    return $this->options = array_replace_recursive($this->options, $options);
  }



  /**
   * Splits the fully qualified name of the entity class into it's namespace and
   * base name, returning an array (to be used as a list of variables by the caller)
   * of these parts along with the full name.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return array
   */
  protected function parseClassName(EntityInterface $entity) {
    $fullClassName = get_class($entity);
    $namespace     = join('\\', array_slice(explode('\\', $fullClassName), 0, -1));
    $className     = join('', array_slice(explode('\\', $fullClassName), -1));

    return [ $fullClassName, $namespace, $className ];
  }

  /**
   * Attempts to derive the model class based on the storage driver of the type
   * of repository being created.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return string
   */
  protected function deriveModelName(EntityInterface $entity) {
    $driver = $this->driver;

    if ($entity instanceof Model) {
      return get_class($entity);
    } else {
      list($fullClassName, $namespace, $classBaseName) = $this->parseClassName($entity);

      $modelClass = "\\${driver}\\${classBaseName}";

      if (trim($modelClass, '\\') !== trim(get_class($entity), '\\') and class_exists($modelClass)) {
        return $modelClass;
      }
    }

    throw new \LogicException(sprintf(
      'A model instance could not be derived from the `%s` entity class passed to the `%s::create()` method',
      get_class($entity),
      get_called_class()
    ));
  }

  /**
   * Attempts to derive the repository class for the current storage driver based
   * on the entity and model passed in.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @return string
   */
  protected function deriveRepositoryName(EntityInterface $entity) {
    list($fullClassName, $namespace, $classBaseName) = array_pad($this->parseClassName($entity), 3, null);

    $repositoryName = null;
    $driver         = $this->driver;
    $className      = sprintf('%s\\%sRepository', $namespace, $classBaseName);
    $choices        = [
      $className,
      "\\${driver}${className}",
    ];

    foreach ($choices as $className) {
      if (class_exists($className)) {
        $repositoryName = $className;

        break;
      }
    }

    if (is_null($repositoryName)) {
      throw new \LogicException("`${driver}Factory::create` could not derive the repository class name for the `${fullClassName}` entity");
    }

    return $repositoryName;
  }

}