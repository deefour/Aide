<?php namespace Deefour\Aide\Persistence\Repository\Factory;

class CsvFactory extends AbstractFactory {

  /**
   * Array of default options for the repository
   *
   * NOTE: `path` IS OMITTED HERE BECAUSE THE DEFAULT IS INTENDED TO BE THE SYSTEM
   * TMP DIRECTORY. PHP SYNTAX DISALLOWS FUNCTION CALLS IN SUCH PROPERTY DECLARATIONS
   *
   * @staticvar
   * @var array
   */
  public static $options = array(
    'extension' => 'csv',
  );



  /**
   * {@inheritdoc}
   */
  public static function create(\Deefour\Aide\Persistence\Entity\EntityInterface $entity, array $options = []) {
    // Workaround to add default path option
    if ( ! array_key_exists('path', $options)) {
      $options['path'] = sys_get_temp_dir();
    }

    $options = array_merge(static::$options, $options);

    list($fullClassName, $namespace, $className) = static::parseClassName($entity);

    $className = sprintf('%s\\Csv\\%sRepository', $namespace, $className);

    return new $className($options);
  }

}