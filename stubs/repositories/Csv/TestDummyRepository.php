<?php namespace Csv;

use Deefour\Aide\Persistence\Repository\CsvRepository;

class TestDummyRepository extends CsvRepository {

  public function __construct(array $options = []) {
    parent::__construct(new TestDummy, $options);
  }

}