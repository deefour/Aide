<?php

class TestArticle extends \Deefour\Aide\Persistence\Entity\AbstractEntity {

  public $title;
  public $slug;
  public $teaser;
  public $body;



  public function policyClass() {
    return get_class() . 'Policy';
  }

}
