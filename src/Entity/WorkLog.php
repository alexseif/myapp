<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * WorkLog.
 *
 * @ORM\Table(name="work_log")
 * @ORM\Entity(repositoryClass="App\Repository\WorkLogRepository")
 */
class WorkLog
{

    use TimestampableEntity;

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
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var float
     *
     * @ORM\Column(name="pricePerUnit", type="float")
     */
    private $pricePerUnit;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    // Ensure the task property is correctly mapped

    /**
     * @ORM\OneToOne(targetEntity="Tasks", inversedBy="workLog")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $task;

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
     * Set duration.
     *
     *  int $duration
     *
     * @return WorkLog
     */
    public function setDuration($duration): WorkLog
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Set pricePerUnit.
     *
     *  float $pricePerUnit
     *
     * @return WorkLog
     */
    public function setPricePerUnit($pricePerUnit): WorkLog
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    /**
     * Get pricePerUnit.
     *
     */
    public function getPricePerUnit(): float|null
    {
        return $this->pricePerUnit;
    }

    /**
     * Set total.
     *
     *
     */
    public function setTotal($total): WorkLog
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * Set task.
     *
     * Tasks $task
     *
     * @return WorkLog
     */
    public function setTask(Tasks $task = null): WorkLog
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task.
     *
     */
    public function getTask(): Tasks
    {
        return $this->task;
    }

    /**
     * Set name.
     *
     *  string $name
     *
     * @return WorkLog
     */
    public function setName($name): WorkLog
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

}
