<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace App\Service;

use App\Entity\Client;
use App\Entity\Contract;
use App\Entity\Days;
use App\Entity\TaskLists;
use App\Entity\Tasks;
use App\Repository\TasksRepository;
use App\Util\WorkWeek;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FocusService.
 *
 * @author Alex Seif <me@alexseif.com>
 */
class FocusService
{

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var array
     */
    protected array $focus = [];

    /**
     * FocusService constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->setTodayHours();
        $this->setCompleted();
        $this->setDayCards();
    }

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

    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function get(): array
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
        $this->focus['completed'] = $this->getTasksRepository()
          ->getCompletedToday();
    }

    public function setDayCards(): void
    {
        $this->focus['dayCards'] = $this->getEm()
          ->getRepository(Days::class)
          ->getImportantCards();
    }

    public function setTasks(Client $client = null): void
    {
        if ($client) {
            $this->focus['client'] = $client;
            $this->focus['tasks'] = $this->getTasksRepository()->focusByClient(
              $client
            );
            $contract = $this->getEm()
              ->getRepository(Contract::class)
              ->findOneBy(['client' => $client]);
            if ($contract) {
                $this->focus['contract'] = $contract;
                $this->focus['todayHours'] = $this->focus['contract']->getHoursPerDay(
                );
            }
        } else {
            $this->focus['tasks'] = $this->getTasksRepository()->focusList();
        }
    }

    public function setTasksByTaskList(TaskLists $tasklist): void
    {
        $this->focus['tasks'] = $this->getTasksRepository()->focusByTasklist(
          $tasklist
        );
    }

}
