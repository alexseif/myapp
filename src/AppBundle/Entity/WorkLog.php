<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * WorkLog.
 *
 * @ORM\Table(name="work_log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkLogRepository")
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

    /**
     * One Task has One WorkLog.
     *
     * @ORM\OneToOne(targetEntity="Tasks", inversedBy="workLog")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;

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
     * Set duration.
     *
     * @param int $duration
     *
     * @return WorkLog
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set pricePerUnit.
     *
     * @param float $pricePerUnit
     *
     * @return WorkLog
     */
    public function setPricePerUnit($pricePerUnit)
    {
        $this->pricePerUnit = $pricePerUnit;

        return $this;
    }

    /**
     * Get pricePerUnit.
     *
     * @return float
     */
    public function getPricePerUnit()
    {
        return $this->pricePerUnit;
    }

    /**
     * Set total.
     *
     * @param float $total
     *
     * @return WorkLog
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set task.
     *
     * @param \AppBundle\Entity\Tasks $task
     *
     * @return WorkLog
     */
    public function setTask(Tasks $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task.
     *
     * @return \AppBundle\Entity\Tasks
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return WorkLog
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
}
