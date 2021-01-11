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
    protected $currencies;
    protected $EGP;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->currencies = $this->em->getRepository('AppBundle:Currency')->findAll();
        $this->EGP = reset($this->currencies);
        foreach ($this->currencies as $currency) {
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

    function getCurrencies()
    {
        return $this->currencies;
    }

    function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

}
