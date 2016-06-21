<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tags
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagsRepository")
 */
class Tags
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
     * @ORM\Column(name="tag", type="string", length=255, unique=true)
     */
    private $tag;

    /**
     * @ORM\ManyToMany(targetEntity="Transactions", mappedBy="tags")
     */
    private $transactions;

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
     * Set tag
     *
     * @param string $tag
     * @return Tags
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
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
     * @param \AppBundle\Entity\Transactions $transactions
     * @return Tags
     */
    public function addTransaction(\AppBundle\Entity\Transactions $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \AppBundle\Entity\Transactions $transactions
     */
    public function removeTransaction(\AppBundle\Entity\Transactions $transactions)
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

    public function __toString()
    {
        return $this->tag;
    }

}
