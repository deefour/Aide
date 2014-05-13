<?php namespace Deefour\Aide\Authorization;

class Policy {

  use PolicyTrait;



  protected $user;

  private $publicApi = [ 'authorize', 'policy', 'scope' ];



  public function __construct($user) {
    $this->user = $user;
  }

  protected function currentUser() {
    return $this->user;
  }



  public static function __callStatic($method, $parameters) {
    $staticMethod = 'get' . ucfirst($method);

    if ( ! method_exists(get_class(), $staticMethod)) {
      throw new \BadMethodCallException(sprintf('A `%s` static method is not defined on `%s`.', $method, get_class()));
    }

    return call_user_func_array('static::' . $staticMethod, $parameters);
  }

  public function __call($method, $parameters) {
    if ( ! in_array($method, $this->publicApi)) {
      throw new \BadMethodCallException(sprintf('A `%s` method is not defined or exposed publicly on `%s`.', $method, get_class()));
    }

    return call_user_func_array([$this, $method], $parameters);
  }

}