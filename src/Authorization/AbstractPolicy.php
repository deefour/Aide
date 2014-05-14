<?php namespace Deefour\Aide\Authorization;

/**
 * Base policy class all application policies are encouraged to extend. Aide
 * expects to pass a user as the first argument to a new policy and the record
 * to authorize against as the second argument.
 */
abstract class AbstractPolicy {

  /**
   * The user to be authorized
   *
   * @var mixed
   */
  protected $user;

  /**
   * The record/object to authorize against
   *
   * @var mixed
   */
  protected $record;


  /**
   * Sets expectations for dependencies on the policy class and stores references
   * to them locally.
   *
   * @param  mixed  $user
   * @param  mixed  $record
   */
  public function __construct($user, $record) {
    $this->user   = $user;
    $this->record = $record;
  }

}