<?php

namespace App\Controller;

use App\Entity\TaskLists;
use App\Entity\Tasks;
use App\Form\TasklistSizingType;
use App\Form\TaskListsType;
use App\Form\TaskSizingType;
use App\Repository\AccountsRepository;
use App\Repository\TaskListsRepository;
use App\Service\RateCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sizing", name="sizing")
 */
class SizingController extends AbstractController
{

    /**
     * @Route("/", name="_index")
     */
    public function index(
      Request $request,
      TaskListsRepository $taskListsRepository,
      RateCalculator $rateCalculator
    ): Response {
        $tasklists = $taskListsRepository->findActive();
        if ($request->get('account')) {
            $account = $request->get('account');
            $tasklists = $taskListsRepository->findBy([
              'account' => $account,
            ]);
        }
        foreach ($tasklists as $tasklist) {
            $tasklist->estTotal = 0;
            $client = ($tasklist->getAccount()) ? $tasklist->getAccount()
              ->getClient() : null;
            $tasklist->rate = ($client) ? $rateCalculator->getRate(
              $client
            ) : $rateCalculator->getDefaultRate();
            foreach ($tasklist->getTasks(false) as $task) {
                $tasklist->estTotal += $task->getEst();
            }
            $tasklist->sizing = number_format(
              $tasklist->estTotal * $tasklist->rate / 60,
              0
            );
        }

        return $this->render('sizing/index.html.twig', [
          'tasklists' => $tasklists,
          'query' => $request->query->all(),
        ]);
    }

    /**
     * @Route ("/{id}", name="_by_tasklist")
     */
    public function sizingByTasklist(
      TaskLists $tasklist,
      Request $request,
      EntityManagerInterface $entityManager
    ): ?Response {
        $request->query->add(['id' => $tasklist->getId()]);
        $query = $request->query->all();
        $tasks = $tasklist->getTasks(false);
        $form = $this->createForm(TasklistSizingType::class, $tasklist, [
          'action' => $this->generateUrl('sizing_by_tasklist', $query),
          'tasks' => $tasks,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Sizing saved');

            return $this->redirect(
              $this->generateUrl('sizing_by_tasklist', $query)
            );
        }

        return $this->render('sizing/tasklist.html.twig', [
          'tasklist' => $tasklist,
          'form' => $form->createView(),
          'query' => $query,
        ]);
    }

    /**
     * @Route("/{id}/new_task", name="_new_task", methods={"GET","POST"})
     *
     * @return JsonResponse|RedirectResponse
     */
    public function newTask(
      TaskLists $tasklist,
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $request->query->add(['id' => $tasklist->getId()]);
        $query = $request->query->all();

        $task = new Tasks();
        $task->setTaskList($tasklist);

        $form = $this->createForm(TaskSizingType::class, $task, [
          'action' => $this->generateUrl('sizing_new_task', $query),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', 'Task saved');

            return $this->redirect(
              $this->generateUrl('sizing_by_tasklist', $query)
            );
        }

        return JsonResponse::create(
          $this->renderView('tasks/_new_sizing.html.twig', [
            'form' => $form->createView(),
            'query' => $query,
          ])
        );
    }

    /**
     * @Route("/new/tasklist", name="_new_tasklist", methods={"GET","POST"})
     *
     * @return JsonResponse|RedirectResponse
     */
    public function newTasklist(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $query = $request->query->all();

        $tasklist = new TaskLists();
        if (array_key_exists('account', $query)) {
            $tasklist->setAccount(
              $this->get(AccountsRepository::class)->find($query['account'])
            );
        }
        $form = $this->createForm(TaskListsType::class, $tasklist, [
          'action' => $this->generateUrl('sizing_new_tasklist', $query),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tasklist);
            $entityManager->flush();
            $this->addFlash('success', 'New Task List saved');

            return $this->redirect(
              $this->generateUrl(
                'sizing_by_tasklist',
                array_merge(['id' => $tasklist->getId()], $query)
              )
            );
        }

        return JsonResponse::create(
          $this->renderView('tasklists/_new_modal.html.twig', [
            'form' => $form->createView(),
            'query' => $query,
          ])
        );
    }

}
