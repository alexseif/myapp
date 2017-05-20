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

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
//    $em = $this->getDoctrine()->getManager();

    $currencies = $this->em->getRepository('AppBundle:Currency')->findAll();
    foreach ($currencies as $currency) {
      $this->currency[$currency->getCode()] = $currency->getEgp() / 100;
    }
  }

  public function get()
  {
    return $this->currency;
  }

}
