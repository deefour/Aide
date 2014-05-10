<?php namespace Deefour\Aide\Persistence\Repository;

use Deefour\Aide\Persistence\Model\ModelInterface;
use Deefour\Aide\Persistence\Entity\EntityInterface;
use Deefour\Aide\Persistence\Model\Csv\Model;



abstract class CsvRepository extends AbstractRepository implements RepositoryInterface {

  /**
   * The full path and filename of the storage for models passing through this
   * repository
   *
   * @var string
   */
  protected $filename;

  /**
   * Collection (array) of model instances found in the storages associated with
   * this repository. This is typically a mirror image of sorts of what is currently
   * in the persistent storage
   *
   * @var array
   */
  protected $records = [];

  /**
   * OO file interface for the persistent storage associated with this repository
   *
   * @var SplFileObject
   */
  protected $file;


  /**
   * @param  Deefour\Aide\Persistence\Model\Csv\Model  $model
   * @param  array                                $options [optional]
   * @throws Exception if the specified filepath for the store is not writable
   */
  public function __construct(ModelInterface $model, array $options = []) {
    $defaultOptions = array(
      'path'      => sys_get_temp_dir(),
      'extension' => 'csv'
    );

    parent::__construct($model, array_replace_recursive($defaultOptions, $options));

    $className      = get_class($this->model);
    $this->filename = sprintf(
      '%s/%s.%s',
      $this->options['path'],
      strtolower(substr($className, strrpos($className, '\\') + 1)),
      $this->options['extension']
    );
    $fileDirectory  = dirname($this->filename);

    if ( ! is_dir($fileDirectory) or ! is_writable($fileDirectory)) {
      throw new \Exception(sprintf('The `%s` path is not writable or does not exist', $fileDirectory));
    }

    $this->file = new \SplFileObject($this->filename, 'a+');

    $this->loadFromFile();
  }



  /**
   * {@inheritdoc}
   */
  public function create(EntityInterface $entity) {
    $model = $this->model->newInstance($entity->toArray());

    $this->records[$model->id] = $model;

    $this->persist($entity, $model);

    return $model;
  }

  /**
   * {@inheritdoc}
   *
   * @throws Exception if the model doesn't yet exist in the store (the in-memory
   * copy is checked)
   */
  public function update(EntityInterface $entity) {
    if ( ! array_key_exists($entity->id, $this->records)) {
      throw new \Exception(sprintf('Model with id `%s` does not exist', $entity->id));
    }

    $model = $this->records[$entity->id];

    $model->fromArray($entity->toArray());

    $this->persist($entity, $model);

    return $model;
  }

  /**
   * {@inheritdoc}
   */
  public function delete(EntityInterface $entity) {
    if ( ! $entity->id) {
      return;
    }

    if (array_key_exists($entity->id, $this->records)) {
      unset($this->records[$entity->id]);
    }

    $this->save();
  }

  /**
   * {@inheritdoc}
   */
  public function find($id) {
    if ( ! array_key_exists($id, $this->records)) {
      return null;
    }

    return $this->records[$id];
  }

  /**
   * {@inheritdoc}
   */
  public function all() {
    return $this->records;
  }

  /**
   * Accessor for the name of the file the repository is reading/writing to/from
   *
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * Writes the record data from memory into the file store.
   *
   * @return void
   */
  public function save() {
    $tmpHandle = new \SplFileObject('php://temp', 'w+');
    $tmpData   = '';

    $tmpHandle->fputcsv($this->model->columns());

    foreach($this->all() as $record) {
      $tmpHandle->fputcsv($record->toArray());
      $record->exists = true;
    }

    $tmpHandle->rewind();

    while ( ! $tmpHandle->eof()) {
      $tmpData .= $tmpHandle->fgets();
    }

    $this->file->flock(\LOCK_EX | \LOCK_NB);
      $this->file->ftruncate(0);
      $this->file->rewind();
      $this->file->fwrite($tmpData);
    $this->file->flock(\LOCK_UN);
  }



  /**
   * Stores all record info to the storage backend and updates the passed entity
   * object with data from the model being persisted.
   *
   * @param  \Deefour\Aide\Persistence\Entity\EntityInterface  $entity
   * @param  \Deefour\Aide\Persistence\Model\Csv\Model  $model
   * @return void
   */
  protected function persist(EntityInterface $entity, Model $model) {
    $this->save();

    $entity->fromArray($model->toArray());
  }

  /**
   * Loads the record/model data from the persistent storage file.
   *
   * NOTE: THE FIRST ROW OF THE FILE MUST CONTAIN COLUMN NAMES MATCHING THE
   * LIST OF COLUMNS ON THE MODEL ASSOCIATED WITH THIS CLASS.
   *
   * @return void
   */
  protected function loadFromFile() {
    $columns = [];

    while ( ! $this->file->eof()) {
      $row = (array)$this->file->fgetcsv();

      if (empty($columns)) {
        $columns = $row;
        continue;
      }

      $row = array_filter($row);

      if (empty($row)) {
        continue;
      }

      $attributes = array_fill_keys($columns, null);

      foreach ($row as $index => $value) {
        $attributes[$columns[$index]] = $value;
      }

      $model = $this->model->newInstance($attributes, true);

      $this->records[$model->id] = $model;
    }
  }

}