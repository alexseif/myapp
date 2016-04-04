<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Accounts
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountsRepository")
 */
class Accounts
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AccountTransactions", mappedBy="account")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity="TaskLists", mappedBy="account")
     */
    private $taskLists;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Accounts
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    

    /**
     * Add transactions
     *
     * @param \AppBundle\Entity\AccountTransactions $transactions
     * @return Accounts
     */
    public function addTransaction(\AppBundle\Entity\AccountTransactions $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \AppBundle\Entity\AccountTransactions $transactions
     */
    public function removeTransaction(\AppBundle\Entity\AccountTransactions $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add taskLists
     *
     * @param \AppBundle\Entity\TaskLists $taskLists
     * @return Accounts
     */
    public function addTaskList(\AppBundle\Entity\TaskLists $taskLists)
    {
        $this->taskLists[] = $taskLists;

        return $this;
    }

    /**
     * Remove taskLists
     *
     * @param \AppBundle\Entity\TaskLists $taskLists
     */
    public function removeTaskList(\AppBundle\Entity\TaskLists $taskLists)
    {
        $this->taskLists->removeElement($taskLists);
    }

    /**
     * Get taskLists
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTaskLists()
    {
        return $this->taskLists;
    }
}
