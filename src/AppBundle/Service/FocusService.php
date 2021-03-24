<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Service;


use AppBundle\Entity\Client;
use AppBundle\Entity\Contract;
use AppBundle\Entity\Days;
use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Repository\TasksRepository;
use AppBundle\Util\WorkWeek;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FocusService
 *
 * @author Alex Seif <me@alexseif.com>
 * @package AppBundle\Service
 */
class FocusService
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $focus = [];


    /**
     * FocusService constructor.
     * @param EntityManagerInterface $em
     */
    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->setTodayHours();
        $this->setCompleted();
        $this->setDayCards();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return TasksRepository|\Doctrine\Persistence\ObjectRepository
     */
    public function getTasksRepository()
    {
        return $this->getEm()->getRepository(Tasks::class);
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->focus;
    }


    /**
     * @param $focus
     */
    public function setFocus($focus): void
    {
        $this->focus = $focus;
    }

    public function setTodayHours(): void
    {
        $this->focus['todayHours'] = WorkWeek::getDayHours(date('l'));
    }

    public function setCompleted(): void
    {
        $this->focus['completed'] = $this->getTasksRepository()->getCompletedToday();
    }

    public function setDayCards()
    {
        $this->focus['dayCards'] = $this->getEm()->getRepository(Days::class)->getImportantCards();
    }

    public function setTasks(Client $client = null): void
    {
        if ($client) {
            $this->focus['client'] = $client;
            $this->focus['tasks'] = $this->getTasksRepository()->focusByClient($client);
            $contract = $this->getEm()->getRepository(Contract::class)->findOneBy(["client" => $client]);
            if ($contract) {
                $this->focus['contract'] = $contract;
                $this->focus['todayHours'] = $this->focus['contract']->getHoursPerDay();
            }
        } else {
            $this->focus['tasks'] = $this->getTasksRepository()->focusList();
        }
    }

    public function setTasksByTaskList(TaskLists $tasklist): void
    {
        $this->focus['tasks'] = $this->getTasksRepository()->focusByTasklist($tasklist);
    }
}
