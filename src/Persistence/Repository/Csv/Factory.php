<?php namespace Deefour\Aide\Persistence\Repository\Csv;

use Deefour\Aide\Persistence\Repository\AbstractFactory;
use Deefour\Aide\Persistence\Entity\EntityInterface;



class Factory extends AbstractFactory {

  /**
   * {@inheritdoc}
   *
   * NOTE: `path` IS OMITTED HERE BECAUSE THE DEFAULT IS INTENDED TO BE THE SYSTEM
   * TMP DIRECTORY. PHP SYNTAX DISALLOWS FUNCTION CALLS IN SUCH PROPERTY DECLARATIONS
   *
   * @var array
   */
  protected $options = array(
    'extension' => 'csv',
  );

  /**
   * {@inheritdoc}
   */
  protected $driver = 'Csv';



  /**
   * {@inheritdoc}
   */
  public function make(EntityInterface $entity, array $options = []) {
    // Workaround to add default path option
    if ( ! array_key_exists('path', $options)) {
      $options['path'] = sys_get_temp_dir();
    }

    $options = array_merge(static::$options, $options);

    return parent::make($entity, $options);
  }

}