<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace App\Service;

use App\Entity\Accounts;
use App\Repository\AccountsRepository;
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

    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     */
    public function getRepository(): AccountsRepository
    {
        return $this->getEm()->getRepository(Accounts::class);
    }

    public function getDashboard()
    {
        return $this->getRepository()->findByNotConceal();
    }

}
