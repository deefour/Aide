<?php namespace Csv;

use Deefour\Aide\Persistence\Repository\CsvRepository;

class TestArticleRepository extends CsvRepository {

  public function __construct(array $options = []) {
    parent::__construct(new TestArticle, $options);
  }

}