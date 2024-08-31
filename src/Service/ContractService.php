<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Tasks;
use App\Model\ContractProgress;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Contract Service.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ContractService
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array progress of contracts
     */
    public function progress(): array
    {
        $contracts = $this->em->getRepository(Contract::class)->findBy(['isCompleted' => false]);
        foreach ($contracts as $contract) {
            $contract->setProgress(new ContractProgress($contract, $this->em->getRepository(Tasks::class)));
        }

        return $contracts;
    }
}
