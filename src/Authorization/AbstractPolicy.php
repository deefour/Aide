<?php namespace Deefour\Aide\Authorization;

abstract class AbstractPolicy {

  protected $user;

  protected $record;



  public function __construct($user, $record) {
    $this->user   = $user;
    $this->record = $record;
  }

}