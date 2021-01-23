<?php


namespace AppBundle\Service;


use AppBundle\Entity\Routine;
use AppBundle\Repository\RoutineRepository;

/**
 * Class RoutineService
 * @package AppBundle\Service
 */
class RoutineService
{
    /**
     *
     * @var RoutineRepository $repository
     */
    protected $repository;

    /**
     * @var Routine|null $current holds the current routine
     */
    protected $current;

    /**
     * RoutineService constructor.
     * @param RoutineRepository $routineRepository
     */
    public function __construct(RoutineRepository $routineRepository)
    {
        $this->setRepository($routineRepository);
        $this->setCurrent($this->getRepository()->findByDayAndTime(date('l'), date('H:i')));
    }

    /**
     * @return RoutineRepository
     */
    public function getRepository(): RoutineRepository
    {
        return $this->repository;
    }

    /**
     * @param RoutineRepository $repository
     */
    public function setRepository(RoutineRepository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return Routine|null
     */
    public function getCurrent(): ?Routine
    {
        return $this->current;
    }

    /**
     * @param Routine|null $current
     */
    public function setCurrent(?Routine $current): void
    {
        $this->current = $current;
    }


}