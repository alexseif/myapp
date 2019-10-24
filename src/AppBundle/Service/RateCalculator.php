<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Tasks;

/**
 * Description of RateCalculator
 *
 * @author Alex Seif <me@alexseif.com>
 */
class RateCalculator
{

  protected $em;
  protected $currencies, $costOfLife, $defaultRate, $cost;

  public function __construct(\Doctrine\ORM\EntityManager $em)
  {
    $this->setEm($em);
    $this->setCurrencies($this->getEm()->getRepository('AppBundle:Currency')->findAll());
    $this->setCost($this->getEm()->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"]);
    $this->setCostOfLife(new \AppBundle\Logic\CostOfLifeLogic($this->getCost(), $this->getCurrencies()));
    $this->setDefaultRate($this->getCostOfLife()->getHourly());
  }

  public function getRate(\AppBundle\Entity\Client $client)
  {
    if ($client->hasRates()) {
      return $client->getRate();
    }
    return $this->getDefaultRate();
  }

  public function getRateByTask(Tasks $task)
  {
    return $this->getRate($task->getTaskList()->getAccount()->getClient());
  }

  public function task(Tasks $task)
  {

    $client = $task->getTaskList()->getAccount()->getClient();

    if ($task->getDuration()) {
      $this->getRate($client) * $task->getDuration();
    }
    return null;
  }

  public function tasks($tasks)
  {
    $total = 0;
    foreach ($tasks as $task) {
      $rate = $this->getRateByTask($task);
      $total += $task->getDuration()/60 * $rate;
    }
    return $total;
  }

  /**
   * 
   * @return \Doctrine\ORM\EntityManager 
   */
  public function getEm()
  {
    return $this->em;
  }

  /**
   * 
   * @param \Doctrine\ORM\EntityManager $em
   */
  public function setEm($em)
  {
    $this->em = $em;
  }

  function getCurrencies()
  {
    return $this->currencies;
  }

  function getDefaultRate()
  {
    return $this->defaultRate;
  }

  function setCurrencies($currencies)
  {
    $this->currencies = $currencies;
  }

  function setDefaultRate($defaultRate)
  {
    $this->defaultRate = $defaultRate;
  }

  function getCostOfLife()
  {
    return $this->costOfLife;
  }

  function setCostOfLife($costOfLife)
  {
    $this->costOfLife = $costOfLife;
  }

  function getCost()
  {
    return $this->cost;
  }

  function setCost($cost)
  {
    $this->cost = $cost;
  }

}
