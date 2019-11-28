<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContractRepository")
 */
class Contract
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
   * @ORM\ManyToOne(targetEntity="Client", inversedBy="contract")
   * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
   */
  private $client;

  /**
   * @var int
   *
   * @ORM\Column(name="hoursPerDay", type="integer")
   */
  private $hoursPerDay;

  /**
   * Get id.
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set name.
   *
   * @param string $name
   *
   * @return Contract
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }


  /**
   * Set account.
   *
   * @param \stdClass|null $account
   *
   * @return Contract
   */
  public function setAccount($account = null)
  {
    $this->account = $account;

    return $this;
  }

  /**
   * Get account.
   *
   * @return \stdClass|null
   */
  public function getAccount()
  {
    return $this->account;
  }

  /**
   * Set hoursPerDay.
   *
   * @param int $hoursPerDay
   *
   * @return Contract
   */
  public function setHoursPerDay($hoursPerDay)
  {
    $this->hoursPerDay = $hoursPerDay;

    return $this;
  }

  /**
   * Get hoursPerDay.
   *
   * @return int
   */
  public function getHoursPerDay()
  {
    return $this->hoursPerDay;
  }


    /**
     * Set client.
     *
     * @param \AppBundle\Entity\Client|null $client
     *
     * @return Contract
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \AppBundle\Entity\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }
}
