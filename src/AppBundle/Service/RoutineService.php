<?php


namespace AppBundle\Service;


use AppBundle\Repository\RoutineRepository;

class RoutineService
{
    /**
     *
     * @var RoutineRepository $repository
     */
    protected $repository;

    public function __construct(RoutineRepository $routineRepository)
    {
        $this->repository = $routineRepository;
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


}