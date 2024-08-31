<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Model\Scheduler;
use App\Repository\TasksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
    public function index(Request $request, EntityManagerInterface $entityManager, $year, $week): Response
    {

        $form = $this->createFormBuilder()
            ->add('concatenate', CheckboxType::class)
            ->setMethod('GET')
            ->getForm();
        $form->handleRequest($request);
        $data = $form->getData();
        $scheduler = new Scheduler($entityManager, $year, $week);
        $scheduler->generate($data ? $data['concatenate']: false);
        return $this->render('scheduler/index.html.twig', [
            'form' => $form->createView(),
            'week' => $week,
            'year' => $year,
            'scheduler' => $scheduler,
        ]);
    }

    private function saveSchedules(
        TasksRepository $tasksRepository,
        EntityManagerInterface $entityManager,
        $scheduleItem
    ): void {
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

    /**
     * @Route("/save", name="scheduler_save")
     */
    public function save(Request $request, TasksRepository $tasksRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            foreach ($request->get('data') as $scheduleItems) {
                foreach ($scheduleItems as $scheduleItem) {
                    $this->saveSchedules($tasksRepository, $entityManager, $scheduleItem);
                }
            }
            $entityManager->flush();
        }

        return new JsonResponse();
    }

    /**
     * @Route("/delete", name="scheduler_delete")
     */
    public function delete(Request $request, TasksRepository $tasksRepository, EntityManagerInterface $entityManager): JsonResponse
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
