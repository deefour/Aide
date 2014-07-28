<?php namespace Deefour\Aide\Authorization;

/**
 * Provides easy access to much of the functionality available in Aide's
 * authorization component.
 *
 * With an instance, `make` can be called, passing in only the object to derive
 * the policy or scope for.
 *
 *   $policyClass = new Policy(Auth::user());
 *   $policyClass->make(new Article); //=>  ArticlePolicy
 *
 * Policy/scope lookups and authorization can also be performed against an
 * instance using a select subset of methods found on the
 * `Deefour\Aide\Authorization\PolicyTrait` class.
 *
 *   $policyClass->authorize(new Article, 'create'); //=> boolean
 *   $policyClass->policy(new Article); //=> ArticlePolicy
 *   $policyClass->scope(new Article);  //=> ArticleScope
 *
 * Alternatively, select methods are exposed statically
 *
 *   Policy::scope(new Article); //=> ArticlePolicy
 *   Policy::policyOrFail(new ObjectWithoutPolicy); //=> NotDefinedException
 */
class Policy {

  // pull in functionality from the PolicyTrait
  use PolicyTrait;


  /**
   * The current user
   *
   * @protected
   * @var mixed
   */
  protected $user;

  /**
   * Options to modify the context of the policy class
   *
   * @protected
   * @var array
   */
  protected $options;

  /**
   * List of methods on the trait to expose publicly
   *
   * @protected
   * @var array
   */
  protected $publicApi = [ 'authorize', 'policy', 'scope' ];



  /**
   * Configure the policy class with the current user and context
   *
   * @param  mixed  $user
   * @param  array  $options [optional]
   */
  public function __construct($user, array $options = []) {
    $this->user    = $user;
    $this->options = $options;
  }

  /**
   * Alias for the trait's policy method. Exists primarily for consistency with
   * other Aide components, like the repository factory's make method to create
   * new repository classes based on an entity.
   *
   * @param  mixed  $record
   * @return  Deefour\Aide\Authorization\AbstractPolicy
   */
  public function make($record) {
    return $this->policy($record);
  }



  /**
   * {@inheritdoc}
   */
  protected function currentUser() {
    return $this->user;
  }



  /**
   * Magic `__callStatic` method, providing access to accessor methods on the
   * policy trait without the need to use the `get` prefix. For example,
   *
   *   Policy::scope(new Article); //=> ArticleScope
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public static function __callStatic($method, array $parameters) {
    $staticMethod = 'get' . ucfirst($method);

    if ( ! method_exists(get_class(), $staticMethod)) {
      throw new \BadMethodCallException(sprintf('A `%s` static method is not defined on `%s`.', $method, get_class()));
    }

    return call_user_func_array('static::' . $staticMethod, $parameters);
  }

  /**
   * Magice `_call` method, providing access to a specific subset of protected
   * methods defined on the policy trait
   *
   * @param  string  $method
   * @param  array   $parameters
   * @return mixed
   */
  public function __call($method, array $parameters) {
    if ( ! in_array($method, $this->publicApi)) {
      throw new \BadMethodCallException(sprintf('A `%s` method is not defined or exposed publicly on `%s`.', $method, get_class()));
    }

    return call_user_func_array([$this, $method], $parameters);
  }

}
