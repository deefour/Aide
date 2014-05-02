<?php namespace Deefour\Aide\Persistence\Repository\Factory;

class EloquentFactory extends AbstractFactory {

  /**
   * {@inheritdoc}
   */
  public static function create(\Deefour\Aide\Persistence\Entity\EntityInterface $entity, array $options = []) {
    $options = array_merge(static::$options, $options);

    list($fullClassName, $namespace, $className) = static::parseClassName($entity);

    $className = sprintf('%s\\%sRepository', $namespace, $className);

    if ( ! class_exists($className)) {
      throw new \Exception("`EloquentFactory::create` could not instantiate an instance of `${className}` for the `${fullClassName}` entity");
    }

    return new $className($entity, $options);
  }

}