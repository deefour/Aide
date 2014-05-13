<?php

use Deefour\Aide\Authorization\AbstractScope;



class TestArticleScope extends AbstractScope {

  public function resolve() {
    return 'scoped';
  }

}