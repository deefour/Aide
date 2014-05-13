<?php namespace Deefour\Aide\Authorization;

class Finder {

  const FINDER_TYPE_POLICY = 'policy';

  const FINDER_TYPE_SCOPE  = 'scope';



  protected $object;



  public function __construct($object) {
    $this->object = $object;
  }

  public function scope() {
    return $this->find(self::FINDER_TYPE_SCOPE);
  }

  public function policy() {
    return $this->find(self::FINDER_TYPE_POLICY);
  }

  public function scopeOrFail() {
    $scope = $this->scope();

    if (class_exists($scope)) {
      return $scope;
    }

    throw new NotDefinedException(sprintf('Unable to find scope `%s` for `%s`', $scope, get_class($this->object)));
  }

  public function policyOrFail() {
    $policy = $this->policy();

    if (class_exists($policy)) {
      return $policy;
    }

    throw new NotDefinedException(sprintf('Unable to find policy `%s` for `%s`', $policy, get_class($this->object)));
  }

  protected function find($type) {
    if (method_exists($this->object, "${type}Class")) {
      $klass = $this->object->policyClass();
    } else {
      if (method_exists($this->object, 'name')) {
        $klassPrefix = $this->object->name();
      } else {
        $klassPrefix = get_class($this->object);
      }

      $klass = $klassPrefix . ucfirst($type);
    }

    return $klass;
  }

}