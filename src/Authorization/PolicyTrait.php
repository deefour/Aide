<?php namespace Deefour\Aide\Authorization;

trait PolicyTrait {

  protected $_policyScoped;

  protected $_policyAuthorized;



  protected static function getPolicyScope($user, $scope) {
    $policyScope = (new Finder($scope))->scope();

    return $policyScope ? (new $policyScope($user, $scope))->resolve() : null;
  }

  protected static function getPolicy($user, $record) {
    $policy = (new Finder($record))->policy();

    return $policy ? new $policy($user, $record) : null;
  }

  protected static function getPolicyScopeOrFail($user, $scope) {
    $policyScope = (new Finder($scope))->scopeOrFail();

    return (new $policyScope($user, $scope))->resolve();
  }

  protected static function getPolicyOrFail($user, $record) {
    $policy = (new Finder($record))->policyOrFail();

    return new $policy($user, $record);
  }



  protected function verifyAuthorized() {
    if ( ! $this->_policyAuthorized) {
      throw new AuthorizationNotPerformedException;
    }
  }

  protected function verifyPolicyScoped() {
    if ( ! $this->_policyScoped) {
      throw new AuthorizationNotPerformedException;
    }
  }

  protected function authorize($record, $method = null) {
    $className = get_class($record);

    $this->_policyAuthorized = true;

    if (is_null($method)) {
      $method = debug_backtrace(false)[1]['function'];
    }

    $policy = $this->policy($record);

    if ( ! $policy->$method()) {
      $exception = new NotAuthorizedException("Not allowed to `${method}` this `${className}`");

      $exception->method = $method;
      $exception->policy = $policy;
      $exception->record = $record;

      throw $exception;
    }

    return true;
  }

  protected function policyScope($scope) {
    $this->_policyScoped = true;

    return static::getPolicyScopeOrFail($this->currentUser(), $scope);
  }

  protected function policy($record) {
    return static::getPolicyOrFail($this->currentUser(), $record);
  }



  abstract protected function currentUser();

}