<?php namespace Deefour\Aide\Persistence\Repository\Eloquent;

use Deefour\Aide\Persistence\Repository\AbstractFactory;
use \Deefour\Aide\Persistence\Entity\EntityInterface;
use \Deefour\Aide\Persistence\Model\Eloquent\Model;

class Factory extends AbstractFactory {

  /**
   * {@inheritdoc}
   */
  protected $driver = 'Eloquent';

}
