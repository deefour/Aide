<?php namespace Csv;

use Deefour\Aide\Persistence\Model\Csv\Model;

class TestArticle extends Model {

  public function columns() {
    return array_merge($this->columns, [ 'title', 'slug', 'teaser', 'body' ]);
  }

}