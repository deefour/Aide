<?php

class AuthorizationController {

  use Deefour\Aide\Authorization\PolicyTrait;



  public function __construct() { }



  public function edit() {
    $this->authorize(new TestArticle);
    $this->scope(new TestArticle);
  }

  // passthru for protected trait methods
  public function __call($method, array $parameters = []) {
    return call_user_func_array([$this, $method], $parameters);
  }

  protected function currentUser() {
    return new \TestDummy;
  }

}
