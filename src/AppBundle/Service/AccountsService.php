<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Service;

use AppBundle\Entity\Accounts;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of AccountsService
 *
 * @author Alex Seif <me@alexseif.com>
 */
class AccountsService
{

    protected $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    function getEm()
    {
        return $this->em;
    }

    /**
     *
     * @return \AppBundle\Repository\AccountsRepository
     */
    public function getRepository()
    {
        return $this->getEm()->getRepository('AppBundle:Accounts');
    }

    public function getDashboard()
    {
        return $this->getRepository()->findBy(array('conceal' => false));
    }

}
