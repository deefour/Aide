<?php namespace Deefour\Aide\Persistence\Repository\Factory;

use \Deefour\Aide\Persistence\Entity\EntityInterface;
use \Deefour\Aide\Persistence\Model\Eloquent\Model;



class EloquentFactory extends AbstractFactory {

  /**
   * {@inheritdoc}
   */
  protected static $driver = 'Eloquent';

}