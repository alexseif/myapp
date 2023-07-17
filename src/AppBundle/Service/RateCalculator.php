<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Client;
use AppBundle\Entity\CostOfLife;
use AppBundle\Entity\Currency;
use AppBundle\Entity\Rate;
use AppBundle\Entity\Tasks;
use AppBundle\Logic\CostOfLifeLogic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of RateCalculator.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class RateCalculator
{
    protected $em;
    protected $currencies;
    protected $costOfLife;
    protected $defaultRate;
    protected $cost;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEm($em);
        $this->setCurrencies($this->getEm()->getRepository(Currency::class)->findAll());
        $this->setCost($this->getEm()->getRepository(CostOfLife::class)->sumCostOfLife()['cost']);
        $this->setCostOfLife(new CostOfLifeLogic($this->getCost(), $this->getCurrencies()));
        $this->setDefaultRate($this->getCostOfLife()->getHourly());
    }

    public function getRate(Client $client)
    {
        if ($client->hasRates()) {
            foreach ($client->getRates() as $rate) {
                if ($rate->getActive()) {
                    return $rate->getRate();
                }
            }
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
            return $this->getRate($client) * $task->getDuration();
        }

        return null;
    }

    public function tasks($tasks)
    {
        $total = 0;
        foreach ($tasks as $task) {
            $rate = $this->getRateByTask($task);
            $total += $task->getDuration() / 60 * $rate;
        }

        return $total;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }

    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function getDefaultRate()
    {
        return $this->defaultRate;
    }

    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    public function setDefaultRate($defaultRate)
    {
        $this->defaultRate = $defaultRate;
    }

    public function getCostOfLife()
    {
        return $this->costOfLife;
    }

    public function setCostOfLife($costOfLife)
    {
        $this->costOfLife = $costOfLife;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return ArrayCollection Rates
     */
    public function getActive()
    {
        return $this->em->getRepository(Rate::class)->getActiveRates();
    }

    public function increaseByPercent(float $percent)
    {
        $rates = $this->getActive();
        foreach ($rates as $rate) {
            $newRate = new Rate();
            $newRate
                ->setActive(true)
                ->setClient($rate->getClient())
                ->setRate($rate->getRate() * $percent);
            $rate->setActive(false);
            $this->em->persist($newRate);
        }
        $this->em->flush();
    }

    /**
     * @param float $fixedValue
     */
    public function increaseByFixedValue($fixedValue, $note)
    {
        $rates = $this->getActive();
        foreach ($rates as $rate) {
            $newRate = new Rate();
            $newRate
                ->setNote($note)
                ->setActive(true)
                ->setClient($rate->getClient())
                ->setRate($rate->getRate() + $fixedValue);
            $rate->setActive(false);
            $this->em->persist($newRate);
        }
        $this->em->flush();
    }
}
