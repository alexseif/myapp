<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Currency.
 *
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{

    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=3, unique=true)
     */
    private string $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var float
     *
     * @ORM\Column(name="egp", type="float")
     */
    private float $EGP;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Currency
     */
    public function setCode(string $code): Currency
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Currency
     */
    public function setName(string $name): Currency
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set usd.
     *
     * @param float $egp
     *
     * @return Currency
     */
    public function setEgp(float $egp): Currency
    {
        $this->EGP = $egp;

        return $this;
    }

    /**
     * Get usd.
     *
     * @return float
     */
    public function getEgp(): float
    {
        return $this->EGP;
    }

    public function __toString()
    {
        return $this->code;
    }

}
