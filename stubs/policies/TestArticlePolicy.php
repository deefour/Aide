<?php

use Deefour\Aide\Authorization\AbstractPolicy;

class TestArticlePolicy extends AbstractPolicy {

  public function edit() {
    return true;
  }

  public function destroy() {
    return false;
  }

  public function permittedAttributes() {
    return [ 'title' ];
  }

}
