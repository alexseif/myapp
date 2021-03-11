<?php

namespace AppBundle\Controller;

use DateInterval;
use \DateTime;
use AppBundle\Entity\Tasks;
use AppBundle\Form\TasksFilterType;
use AppBundle\Form\TasksType;
use AppBundle\Repository\TasksRepository;
use AppBundle\Util\Paginator;
use Doctrine\Common\Collections\ArrayCollection;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/tasks")
 */
class TasksController extends Controller
{
    /**
     * @Route("/", name="tasks_index", methods={"GET"})
     */
    public function index(TasksRepository $tasksRepository, Request $request): Response
    {
        $page = $request->get('page', 0);
        $paginator = new Paginator('tasks_index', $tasksRepository->findAllWithJoinsQuery(),
            100, $page);

        $tasks = $tasksRepository->findAllWithJoins($paginator->getLimit(), $paginator->getOffset());

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
            'paginator' => $paginator
        ]);
    }

    /**
     * Reorders Tasks entity.
     *
     * @Route("/reorder", name="tasks_reorder", methods={"GET"})
     */
    public function reorderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tasksRepo = $em->getRepository('AppBundle:Tasks');
        $weightedList = $tasksRepo->weightedList();
        $taskListsOrder = [];
        foreach ($weightedList as $key => $row) {
            if (!in_array($row['id'], $taskListsOrder)) {
                $taskListsOrder[] = $row['id'];
            }
        }
        $tasks = new ArrayCollection();
        foreach ($taskListsOrder as $taskListId) {
            $reorderTasks = $tasksRepo->findBy(
                array(
                    'taskList' => $taskListId
                ), array(
                    'urgency' => 'DESC',
                    'priority' => 'DESC',
                    'order' => 'ASC',
                    'est' => 'ASC'
                )
            );

            $tasks->add($reorderTasks);
        }

        return new JsonResponse($tasks);
    }

    /**
     * Search all Tasks entities.
     *
     * @Route("/progressByDate", name="tasks_progress_by_date", methods={"GET"})
     */
    public function progressByDateAction(TasksRepository $tasksRepository, Request $request): Response
    {
        $formData = new stdClass();
        $formData->date = new DateTime();
        $formData->date->modify('-1 day');

        $form = $this->createFormBuilder($formData)
            ->setMethod('GET')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => false])
            ->add('Get', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        $completedYesterday = $tasksRepository->getCompletedByDate($formData->date);
        $createdYesterday = $tasksRepository->getCreatedByDate($formData->date);
        $updatedYesterday = $tasksRepository->getUpdatedByDate($formData->date);
        // Starting by yesterday

        return $this->render("AppBundle:tasks:progressByDate.html.twig", [
            'form' => $form->createView(),
            'date' => $formData->date,
            'completedYesterday' => $completedYesterday,
            'createdYesterday' => $createdYesterday,
            'updatedYesterday' => $updatedYesterday
        ]);
    }

    /**
     * Search all Tasks entities.
     *
     * @Route("/search", name="tasks_search", methods={"GET"})
     */
    public function searchAction(TasksRepository $tasksRepository, Request $request): Response
    {
        $form = $this->createForm(TasksFilterType::class, $request->get('tasks_filter'), array(
            'method' => 'GET',
        ));
        $filters = $tasks = array();
        if ($request->query->has($form->getName())) {
            $filters = $request->get('tasks_filter');
            $tasksQuery = $tasksRepository->createQueryBuilder('t')
                ->select('t, tl, acc, c, w')
                ->join('t.taskList', 'tl')
                ->leftJoin('tl.account', 'acc')
                ->leftJoin('acc.client', 'c')
                ->leftJoin('t.workLog', 'w');

            $firstWhere = true;
            foreach ($filters as $key => $value) {
                if ($firstWhere) {
                    $firstWhere = false;
                    $tasksQuery->where($tasksQuery->expr()->in('t.' . $key, ':' . $key));
                } else {
                    $tasksQuery->andWhere($tasksQuery->expr()->in('t.' . $key, ':' . $key));
                }
                $tasksQuery->setParameter($key, $value);
            }

            $tasks = $tasksQuery->getQuery()->getResult();
        }

        return $this->render("AppBundle:tasks:search.html.twig", array(
            'filters' => $filters,
            'tasks' => $tasks,
            'task_filter_form' => $form->createView(),
        ));
    }


    /**
     * Advanced Lists all Tasks entities.
     *
     * @Route("/advanced", name="tasks_advanced", methods={"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function advancedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Tasks')->findBy(array(
            "completed" => false
        ), array(
            "priority" => "DESC",
            "urgency" => "DESC",
            "order" => "ASC",
            "duration" => "ASC"
        ));

        return $this->render("AppBundle:tasks:advanced.html.twig", array(
            'tasks' => $tasks,
        ));
    }

    /**
     * Displays a form to edit an existing Tasks entity.
     *
     * @Route("/order", name="tasks_order", methods={"POST"})
     */
    public function orderAction(Request $request)
    {

        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $tasks = $request->get('tasks');
            foreach ($tasks as $order => $taskId) {
                $task = $em->find(Tasks::class, $taskId);
                if ($task) {
                    $task->setOrder($order);
                }
            }
            $em->flush();
        }
        return new JsonResponse();
    }

    /**
     * @Route("/new", name="tasks_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $task = new Tasks();
        if (!empty($request->get("tasklist"))) {
            $taskList = $em->getRepository('AppBundle:TaskLists')->find($request->get("tasklist"));
            $task->setTaskList($taskList);
        }
        if (!empty($request->get("duration"))) {
            $task->setDuration($request->get("duration"));
        }
        if (!empty($request->get("completedAt"))) {
            $task->setCompleted(true);
            $task->setCompletedAt(new DateTime($request->get("completedAt")));
        }
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($task->getCompleted()) {
                if (is_null($task->getCompletedAt()))
                    $task->setCompletedAt(new DateTime());
            } else {
                $task->setCompletedAt(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('tasks_index');
        }

        return $this->render('tasks/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tasks_show", methods={"GET"})
     */
    public function show(Tasks $task): Response
    {
        return $this->render('tasks/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tasks_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tasks $task): Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXMLHttpRequest()) {

            if (!is_null($request->get('postpone'))) {
                $postpone = $request->get('postpone');
                $eta = new DateTime($request->get('postpone'));
                if ("tomorrow" === $postpone) {
                    $eta->setTime(8, 0, 0);
                }
                $task->setEta($eta);
            }

            if (!is_null($request->get('undo'))) {
                $task->setEta(null);
            }
            if (!is_null($request->get('completed'))) {
                $task->setCompleted($request->get('completed'));
                $task->setDuration($request->get('duration'));
                if ($task->getCompleted()) {
                    $task->setCompletedAt(new DateTime());
                } else {
                    $task->setCompletedAt(null);
                }
            }
            $em->flush();
            return new JsonResponse();
        }

        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ("archive" === $request->get('completedAt')) {
                $task->setCompleted(true);
                $lastMonth = new DateTime();
                $lastMonth->sub(new DateInterval('P1M'));
                $task->setCompletedAt($lastMonth);
            } else {
                if ($task->getCompleted()) {
                    if (null == $task->getCompletedAt()) {
                        $task->setCompletedAt(new DateTime());
                    }
                } else {
                    $task->setCompletedAt(null);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tasks_show', array('id' => $task->getId()));

//            return $this->redirectToRoute('tasks_index');
        }

        return $this->render('tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tasks_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tasks $task): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tasks_index');
    }

    /**
     * Creates a form to delete a Tasks entity.
     *
     * @param Tasks $task The Tasks entity
     *
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Tasks $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tasks_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
