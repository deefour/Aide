<?php namespace Deefour\Aide\Authorization;

/**
 * Derives the full class name for a policy or scope based on a passed object.
 *
 * There is support for silent or noisy failure if a class could not be provided.
 *
 * This class does not actually instantiate the derived class - it simply returns
 * the class' name
 */
class Finder {

  /**
   * A flag telling the finder to derive a policy class name
   *
   * @const
   * @var string
   */
  const FINDER_TYPE_POLICY = 'policy';

  /**
   * A flag telling the finder to derive a scope class name
   *
   * @const
   * @var string
   */
  const FINDER_TYPE_SCOPE  = 'scope';



  /**
   * The object to derive the scope or policy class name from
   *
   * @protected
   * @var mixed
   */
  protected $object;



  public function __construct($object) {
    $this->object = $object;
  }

  /**
   * Derives a scope class name for the object the finder was passed when
   * instantiated. There is no check made here to see if the class actually exists.
   *
   * @return string
   */
  public function scope() {
    return $this->find(self::FINDER_TYPE_SCOPE);
  }

  /**
   * Derives a policy class name for the object the finder was passed when
   * instantiated. There is no check made here to see if the class actually exists.
   *
   * @return string
   */
  public function policy() {
    return $this->find(self::FINDER_TYPE_POLICY);
  }

  /**
   * Derives a scope class name for the object the finder was passed when
   * instantiated. There is no check made here to see if the class actually exists.
   *
   * Fails loudly if the derived scope class does not exist.
   *
   * @throws Deefour\Aide\Authorization\NotDefinedException
   * @return string
   */
  public function scopeOrFail() {
    $scope = $this->scope();

    if (class_exists($scope)) {
      return $scope;
    }

    throw new NotDefinedException(sprintf('Unable to find scope `%s` for `%s`', $scope, get_class($this->object)));
  }

  /**
   * Derives a policy class name for the object the finder was passed when
   * instantiated. There is no check made here to see if the class actually exists.
   *
   * Fails loudly if the derived policy class does not exist.
   *
   * @throws Deefour\Aide\Authorization\NotDefinedException
   * @return string
   */
  public function policyOrFail() {
    $policy = $this->policy();

    if (class_exists($policy)) {
      return $policy;
    }

    throw new NotDefinedException(sprintf('Unable to find policy `%s` for `%s`', $policy, get_class($this->object)));
  }



  /**
   * Derives the class name for the object the finder was passed when instantiated.
   *
   * @param  $type  string
   * @return string
   */
  protected function find($type) {
    if (method_exists($this->object, "${type}Class")) {
      $klass = $this->object->policyClass();
    } else {
      if (method_exists($this->object, 'name')) {
        $classPrefix = $this->object->name();
      } else {
        $classPrefix = get_class($this->object);
      }

      $klass = $classPrefix . ucfirst($type);
    }

    return $klass;
  }

}