<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Model\ContractProgress;
use AppBundle\Util\DateRanges;
use Doctrine\ORM\EntityManager;

/**
 * Description of Contract Service
 *
 * @author Alex Seif <me@alexseif.com>
 */
class ContractService
{

    /**
     *
     * @var EntityManager $em
     */
    protected $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @return array progress of contracts
     */
    public function progress()
    {
        $contracts = $this->em->getRepository('AppBundle:Contract')->findBy(["isCompleted" => false]);
        foreach ($contracts as $contract) {
            $contract->setProgress(new ContractProgress($contract, $this->em->getRepository('AppBundle:Tasks')));
        }
        return $contracts;
    }

}
