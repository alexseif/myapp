<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Schedule;
use AppBundle\Model\Scheduler;
use AppBundle\Repository\TasksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scheduler")
 */
class SchedulerController extends AbstractController
{
    /**
     * @Route("/", name="scheduler_landing")
     */
    public function landing(): Response
    {
        $date = new DateTime();
        $year = $date->format('Y');
        $week = $date->format('W');
        if (7 === $date->format('N')) {
            ++$week;
        }

        return $this->redirect($this->generateUrl('scheduler', ['year' => $year, 'week' => $week]));
    }

    /**
     * @Route("/{year}/{week}", name="scheduler")
     */
    public function index(EntityManagerInterface $entityManager, $year, $week): Response
    {
        $scheduler = new Scheduler($entityManager, $year, $week);

        return $this->render('scheduler/index.html.twig', [
            'week' => $week,
            'year' => $year,
            'scheduler' => $scheduler,
        ]);
    }

    /**
     * @Route("/save", name="scheduler_save")
     */
    public function save(Request $request, TasksRepository $tasksRepository, EntityManagerInterface $entityManager)
    {
        if ($request->isXmlHttpRequest()) {
            foreach ($request->get('data') as $scheduleItems) {
                foreach ($scheduleItems as $scheduleItem) {
                    $task = $tasksRepository->find($scheduleItem['task']);
                    if (!$task->getCompleted()) {
                        $schedule = new Schedule();
                        $schedule->setSchedule(
                            $scheduleItem['id'] ?: null,
                            $task,
                            $scheduleItem['est'],
                            new DateTime($scheduleItem['eta'])
                        );
                        if (is_null($schedule->getId())) {
                            $entityManager->persist($schedule);
                        }
                    }
                }
            }
            $entityManager->flush();
        }

        return new JsonResponse();
    }

    /**
     * @Route("/delete", name="scheduler_delete")
     */
    public function delete(Request $request, TasksRepository $tasksRepository, EntityManagerInterface $entityManager)
    {
        if ($request->isXmlHttpRequest()) {
            $scheduleId = $request->get('schedule_id');
            $schedule = $entityManager->getRepository(Schedule::class)->find($scheduleId);
            $entityManager->remove($schedule);
            $entityManager->flush();
        }

        return new JsonResponse();
    }
}
