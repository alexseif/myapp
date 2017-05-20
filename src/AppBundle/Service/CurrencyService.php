<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Description of CurrencyService
 *
 * @author Alex Seif <me@alexseif.com>
 */
class CurrencyService
{

  protected $em;
  protected $currency;
  protected $EGP;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
    $currencies = $this->em->getRepository('AppBundle:Currency')->findAll();
    $this->EGP = reset($currencies);
    foreach ($currencies as $currency) {
      $this->currency[$currency->getCode()] = $currency->getEgp() / 100;
    }
  }

  public function get()
  {
    return $this->currency;
  }

  public function getEgp()
  {
    return $this->EGP;
  }

}
