<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Accounts;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of AccountsService.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class AccountsService
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return \AppBundle\Repository\AccountsRepository
     */
    public function getRepository()
    {
        return $this->getEm()->getRepository(Accounts::class);
    }

    public function getDashboard()
    {
        return $this->getRepository()->findBy(['conceal' => false]);
    }
}
