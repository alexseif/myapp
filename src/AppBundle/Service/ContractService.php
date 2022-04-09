<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Tasks;
use AppBundle\Model\ContractProgress;
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
    public function progress()
    {
        $contracts = $this->em->getRepository('AppBundle:Contract')->findBy(['isCompleted' => false]);
        foreach ($contracts as $contract) {
            $contract->setProgress(new ContractProgress($contract, $this->em->getRepository(Tasks::class)));
        }

        return $contracts;
    }
}
