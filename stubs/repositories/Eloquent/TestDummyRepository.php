<?php namespace Eloquent;

use Deefour\Aide\Persistence\Repository\EloquentRepository;

class TestDummyRepository extends EloquentRepository {

  protected $model;



  public function __construct(array $options = []) {
    $this->model = new TestDummy;

    parent::__construct($this->model, $options);
  }

}