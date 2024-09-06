<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\TaskLists;
use App\Entity\Tasks;
use App\Service\FocusService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FocusController extends AbstractController
{

    #[Route("/focus", name:"focus")]
    public function focusAction(
      Request $request,
      FocusService $focusService,
      EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;

        if ($request->query->has('client')) {
            $client = $em->getRepository(Client::class)->find(
              $request->get('client')
            );
            if (empty($client)) {
                throw new NotFoundHttpException('Client not found');
            }
            $focusService->setTasks($client);
        } else {
            $focusService->setTasks();
        }

        return $this->render(
          'focus/index.html.twig',
          $focusService->get()
        );
    }

    #[Route("/focus/{name}", name:"focus_tasklist")]
    public function focusByTaskListAction(
      TaskLists $tasklist,
      FocusService $focusService
    ): ?Response {
        $focusService->setTasksByTaskList($tasklist);

        return $this->render(
          'focus/index.html.twig',
          $focusService->get()
        );
    }

    #[Route("/singleTask", name:"singleTask")]
    public function singleTaskAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;
        $tasksRepo = $em->getRepository(Tasks::class);
        $weightedList = $tasksRepo->weightedList();
        $taskListsOrder = [];
        foreach ($weightedList as $row) {
            if (!in_array($row['id'], $taskListsOrder)) {
                $taskListsOrder[] = $row['id'];
            }
        }
        $tasks = [];
        $reorderTasksList = [];

        foreach ($taskListsOrder as $taskListId) {
            $reorderTasks = $tasksRepo->findBy([
              'taskList' => $taskListId,
              'completed' => false,
            ], [
              'urgency' => 'DESC',
              'priority' => 'DESC',
              'order' => 'ASC',
            ], 10);

            $reorderTasksList[] = $reorderTasks;
        }

        $tasks = array_merge(...$reorderTasksList);

        return $this->render('focus/singleTask.html.twig', [
          'tasks' => $tasks,
        ]);
    }

}
