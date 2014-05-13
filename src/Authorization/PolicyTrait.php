<?php namespace Deefour\Aide\Authorization;

trait PolicyTrait {

  protected $policy;

  protected $_policyScoped;

  protected $_policyAuthorized;



  protected static function getPolicyScope($user, $scope) {
    $policyScope = (new Finder($scope))->scope();

    if ($policyScope) {
      return (new $policyScope($user, $scope))->resolve();
    }
  }

  protected static function getPolicy($user, $record) {
    $policy = (new Finder($record))->policy();

    if ($policy) {
      return new $policy($user, $record);
    }
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

  protected function authorize($entity, $method = null) {
    $className       = get_class($entity);
    $policyClassName = "${className}Policy";

    $this->_policyAuthorized = true;

    if (is_null($method)) {
      list(, $method) = debug_backtrace(false);
    }

    $policy = new $policyClassName($this->currentUser(), $entity);

    if ( ! $policy->$method()) {
      // abort 401 unauthorized
    }
  }

  protected function policyScope($scope) {
    $this->_policyScoped = true;

    return static::getPolicyScopeOrFail($this->currentUser(), $scope);
  }

  protected function policy($entity) {
    return static::getPolicyOrFail($this->currentUser(), $entity);
  }

  protected function currentUser() {
    return Auth::user();
  }

  public function __callStatic($method, $parameters) {
    return forward_static_call_array('get' . ucFirst($method), $parameters);
  }

}