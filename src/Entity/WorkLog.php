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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private int $duration;

    /**
     * @var float
     *
     * @ORM\Column(name="pricePerUnit", type="float")
     */
    private float $pricePerUnit;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private float $total;

    // Ensure the task property is correctly mapped

    /**
     * @ORM\OneToOne(targetEntity="Tasks", inversedBy="workLog")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Tasks $task;

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
