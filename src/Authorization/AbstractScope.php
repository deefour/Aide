<?php namespace Deefour\Aide\Authorization;

/**
 * Base scope class all application scopes are encouraged to extend. Aide
 * expects a `resolve` method to be present on the scope
 */
abstract class AbstractScope {

  /**
   * The user
   *
   * @var mixed
   */
  protected $user;

  /**
   * The
   *
   * @var mixed
   */
  protected $scope;


  /**
   * Sets expectations for dependencies on the policy class and stores references
   * to them locally.
   *
   * @param  mixed  $user
   * @param  mixed  $scope
   */
  public function __construct($user, $scope) {
    $this->user   = $user;
    $this->scope = $scope;
  }

  /**
   *
   */
  abstract public function resolve();

}