<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Client;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\Rate;
use AppBundle\Logic\CostOfLifeLogic;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Description of RateCalculator
 *
 * @author Alex Seif <me@alexseif.com>
 */
class RateCalculator
{

    protected $em;
    protected $currencies, $costOfLife, $defaultRate, $cost;

    public function __construct(EntityManager $em)
    {
        $this->setEm($em);
        $this->setCurrencies($this->getEm()->getRepository('AppBundle:Currency')->findAll());
        $this->setCost($this->getEm()->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"]);
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

    public
    function getRateByTask(Tasks $task)
    {
        return $this->getRate($task->getTaskList()->getAccount()->getClient());
    }

    public
    function task(Tasks $task)
    {

        $client = $task->getTaskList()->getAccount()->getClient();

        if ($task->getDuration()) {
            $this->getRate($client) * $task->getDuration();
        }
        return null;
    }

    public
    function tasks($tasks)
    {
        $total = 0;
        foreach ($tasks as $task) {
            $rate = $this->getRateByTask($task);
            $total += $task->getDuration() / 60 * $rate;
        }
        return $total;
    }

    /**
     *
     * @return EntityManager
     */
    public
    function getEm()
    {
        return $this->em;
    }

    /**
     *
     * @param EntityManager $em
     */
    public
    function setEm($em)
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

    /**
     *
     * @return ArrayCollection Rates
     */
    public
    function getActive()
    {
        return $this->em->getRepository('AppBundle:Rate')->getActiveRates();
    }

    /**
     *
     * @param float $percent
     */
    public
    function increaseByPercent(float $percent)
    {
        $em = $this->em;
        $rates = $this->getActive();
        foreach ($rates as $rate) {
            $newRate = new Rate();
            $newRate
                ->setActive(true)
                ->setClient($rate->getClient())
                ->setRate($rate->getRate() * $percent);
            $rate->setActive(false);
            $em->persist($newRate);
        }
        $em->flush();
    }

    /**
     *
     * @param float $fixedValue
     */
    public
    function increaseByFixedValue($fixedValue, $note)
    {
        $em = $this->em;
        $rates = $this->getActive();
        foreach ($rates as $rate) {
            $newRate = new Rate();
            $newRate
                ->setNote($note)
                ->setActive(true)
                ->setClient($rate->getClient())
                ->setRate($rate->getRate() + $fixedValue);
            $rate->setActive(false);
            $em->persist($newRate);
        }
        $em->flush();
    }

}
