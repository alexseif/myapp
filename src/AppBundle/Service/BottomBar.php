<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Planner;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\CostService;
use AppBundle\Service\ContractService;

/**
 * Description of Bottom Bar Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class BottomBar
{

    /**
     *
     * @var EntityManager $em
     */
    protected $em;

    /**
     *
     * @var CostService $cs
     */
    protected $costService;

    /**
     *
     * @var ContractService $c
     */
    protected $contractService;

    public function __construct(EntityManager $em, CostService $costService, ContractService $contractService)
    {
        $this->em = $em;
        $this->costService = $costService;
        $this->contractService = $contractService;
    }

    function getEm(): EntityManager
    {
        return $this->em;
    }

    function getCostService(): CostService
    {
        return $this->costService;
    }

    function getContractService(): ContractService
    {
        return $this->contractService;
    }

    public function getPlanner()
    {
        $planner = $this->getEm()->getRepository(Planner::class)->findOneBy([], ['createdAt' => 'desc']);
        return $planner;
    }

    /**
     *
     * @return array Thing[]
     */
    public function getThings()
    {
        $things = $this->getEm()->getRepository('AppBundle:Thing')->findBy([], ['createdAt' => 'asc'], 5);
        return $things;
    }

    /**
     *
     * @return array Tasks[]
     */
    public function getTasks()
    {
        return $this->getEm()->getRepository('AppBundle:Tasks')->focusList(5);
    }

    /**
     *
     * @return array Contract[]
     */
    public function getContractsProgress()
    {
        return $this->getContractService()->progress();
    }

    public function getProgress()
    {
        $earnedLogic = new \AppBundle\Logic\EarnedLogic($this->getEm(), $this->getCostService());
        $earned = $earnedLogic->getEarned();
        return [
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $this->costService,
        ];
    }

    public function getObjectives()
    {
        $objectives = $this->getEm()->getRepository('AppBundle:Objective')->findBy([], ['createdAt' => 'asc'], 5);
        return $objectives;
    }

}
