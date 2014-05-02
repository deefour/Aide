<?php namespace Deefour\Aide\Persistence\Repository\Factory;

class CsvFactory extends AbstractFactory {

  /**
   * {@inheritdoc}
   *
   * NOTE: `path` IS OMITTED HERE BECAUSE THE DEFAULT IS INTENDED TO BE THE SYSTEM
   * TMP DIRECTORY. PHP SYNTAX DISALLOWS FUNCTION CALLS IN SUCH PROPERTY DECLARATIONS
   *
   * @staticvar
   * @var array
   */
  protected static $options = array(
    'extension' => 'csv',
  );

  /**
   * {@inheritdoc}
   */
  protected static $driver = 'Csv';



  /**
   * {@inheritdoc}
   */
  public static function create(\Deefour\Aide\Persistence\Entity\EntityInterface $entity, array $options = []) {
    // Workaround to add default path option
    if ( ! array_key_exists('path', $options)) {
      $options['path'] = sys_get_temp_dir();
    }

    $options = array_merge(static::$options, $options);

    return parent::create($entity, $options);
  }

}