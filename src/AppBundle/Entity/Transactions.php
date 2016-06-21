<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransactionsRepository")
 */
class Transactions
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
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="txn_date", type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Categories", inversedBy="transactions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="Tags", inversedBy="transactions")
     * @ORM\JoinTable(name="txn_tags")
     */
    private $tags;

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
     * Set amount
     *
     * @param integer $amount
     * @return Transactions
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount/100;
    }
    /**
     * Get amount
     *
     * @return integer 
     */
    public function getRawAmount()
    {
        return $this->amount;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Transactions
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Categories $category
     * @return Transactions
     */
    public function setCategory(\AppBundle\Entity\Categories $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Categories 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add tags
     *
     * @param \AppBundle\Entity\Tags $tags
     * @return Transactions
     */
    public function addTag(\AppBundle\Entity\Tags $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \AppBundle\Entity\Tags $tags
     */
    public function removeTag(\AppBundle\Entity\Tags $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function makeExpense()
    {
        return $this->amount  = $this->amount * -1;
    }


}
