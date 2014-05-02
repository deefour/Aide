<?php namespace Deefour\Aide\Persistence\Model\Csv;

class User extends Model {

  /**
   * {@inheritdoc}
   */
  protected function columns() {
    return array_merge($this->columns, [ 'first_name', 'last_name', 'email' ]);
  }

}
