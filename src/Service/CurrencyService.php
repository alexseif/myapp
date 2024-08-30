<?php

namespace App\Service;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of CurrencyService.
 */
class CurrencyService
{

    private EntityManagerInterface $em;

    private array $currency = [];

    private Currency $EGP;

    private array $currencies;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->currencies = $this->em->getRepository(Currency::class)->findAll(
        );
        $this->EGP = reset($this->currencies);
        foreach ($this->currencies as $currency) {
            $this->currency[$currency->getCode()] = $currency->getEgp() / 100;
        }
    }

    public function get(): array
    {
        return $this->currency;
    }

    public function getEgp(): Currency
    {
        return $this->EGP;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function setCurrencies(array $currencies): void
    {
        $this->currencies = $currencies;
    }

}