<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Logic;

/**
 * Description of CostOfLifeLogic
 *
 * @author Alex Seif <me@alexseif.com>
 */
class CostOfLifeLogic
{

  public $col;
  public $cost;
  public $currencies;
  public $units = [
    "cost" => ["factor" => 1, "precision" => 0],
    "profit" => ["factor" => 0.2, "precision" => 0],
    "monthly" => ["factor" => 1.2, "precision" => 0],
    "hour" => ["factor" => 1.2 / 122, "precision" => 0],
    "day" => ["factor" => 1.2 / 122 * 6, "precision" => -1],
    "week" => ["factor" => 1.2 / 122 * 31, "precision" => -2],
    "month" => ["factor" => 1.2, "precision" => -2],
    "annually" => ["factor" => 1.2 * 12, "precision" => -2]
  ];

  public function __construct($cost, $currencies)
  {
//    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
//    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];
    $this->cost = $cost;
    $this->currencies = $currencies;
    $this->calc();
  }

  public function calc()
  {
    foreach ($this->currencies as $currency) {
      foreach ($this->units as $unit => $factor) {
        $this->col[$unit][$currency->getCode()] = round($this->cost * $factor["factor"] * ($currency->getEgp() / 100), $factor["precision"]);
      }
    }
  }

  public function getMonthly()
  {
    return $this->col['monthly']['EGP'];
  }

}
