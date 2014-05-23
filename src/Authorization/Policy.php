<?php namespace Deefour\Aide\Authorization;

class Policy {

  use PolicyTrait;



  protected $user;

  protected $options;

  private $publicApi = [ 'authorize', 'policy', 'scope' ];



  public function __construct($user = null, array $options = []) {
    $this->user    = $user;
    $this->options = $options;
  }

  public function make($record) {
    return static::getPolicy($this->currentUser(), $record);
  }



  protected function currentUser() {
    if ($this->user) {
      return $this->user;
    }

    if ( ! array_key_exists('user', $this->options)) {
      throw new \InvalidArgumentException('No `$user` or callable `user` option has been set on the `Policy` class');
    }

    if ( ! is_callable($this->options['user'])) {
      return $this->options['user'];
    }

    return call_user_func($this->options['user']);
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