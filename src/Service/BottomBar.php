<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\Objective;
use App\Entity\Tasks;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Bottom Bar Service.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class BottomBar
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var CostService
     */
    protected $costService;

    /**
     * @var ContractService
     */
    protected $contractService;

    protected $progressMonitoring;

    public function __construct(EntityManagerInterface $em, CostService $costService, ContractService $contractService)
    {
        $this->em = $em;
        $this->costService = $costService;
        $this->contractService = $contractService;
    }

    public function getEm(): EntityManager
    {
        return $this->em;
    }

    public function getCostService(): CostService
    {
        return $this->costService;
    }

    public function getContractService(): ContractService
    {
        return $this->contractService;
    }

    /**
     * @return array Tasks[]
     */
    public function getTasks()
    {
        return $this->getEm()->getRepository(Tasks::class)->focusList(5);
    }

    /**
     * @return array Contract[]
     */
    public function getContractsProgress()
    {
        return $this->getContractService()->progress();
    }

    public function getProgress()
    {
        $earnedLogic = new \Logic\EarnedLogic($this->getEm(), $this->getCostService());
        $earned = $earnedLogic->getEarned();

        return [
            'earned' => $earned,
            'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
            'costOfLife' => $this->costService,
        ];
    }

    public function getObjectives()
    {
        return $this->getEm()->getRepository(Objective::class)->findBy([], ['createdAt' => 'asc'], 5);
    }
}
