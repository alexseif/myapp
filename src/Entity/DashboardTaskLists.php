<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * DashboardTaskLists.
 *
 * @ORM\Table(name="dashboard_task_lists")
 * @ORM\Entity(repositoryClass="App\Repository\DashboardTaskListsRepository")
 */
class DashboardTaskLists
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
     * @ORM\OneToOne(targetEntity="TaskLists")
     * @ORM\JoinColumn(name="taskLists_id", referencedColumnName="id")
     */
    private TaskLists $taskList;

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
     * Set taskList.
     *
     * @paramTaskLists $taskList
     *
     */
    public function setTaskList(TaskLists $taskList = null): DashboardTaskLists
    {
        $this->taskList = $taskList;

        return $this;
    }

    /**
     * Get taskList.
     *
     */
    public function getTaskList(): TaskLists
    {
        return $this->taskList;
    }

    public function getName()
    {
        return $this->taskList->getName();
    }

}
