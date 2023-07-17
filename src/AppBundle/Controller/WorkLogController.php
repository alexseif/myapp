<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountTransactions;
use AppBundle\Entity\CostOfLife;
use AppBundle\Entity\Currency;
use AppBundle\Entity\Rate;
use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\WorkLog;
use AppBundle\Form\WorkLogType;
use AppBundle\Logic\BillingCalculator;
use AppBundle\Logic\CostOfLifeLogic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Worklog controller.
 *
 * @Route("worklog")
 */
class WorkLogController extends AbstractController
{
    /**
     * Lists all workLog entities.
     *
     * @Route("/", name="worklog_index", methods={"GET"})
     */
    public function indexAction(): ?Response
    {
        $em = $this->getDoctrine()->getManager();

        $tasklists = $em->getRepository(TaskLists::class)->findAll();

        return $this->render('AppBundle:worklog:index.html.twig', [
            'tasklists' => $tasklists,
        ]);
    }

    /**
     * Lists all workLog entities.
     *
     * @Route("/tasklist/{tasklist}", name="worklog_tasklist", methods={"GET"})
     */
    public function tasklistAction(TaskLists $tasklist): ?Response
    {
        $em = $this->getDoctrine()->getManager();

        $workLogs = $em->getRepository(WorkLog::class)->getByTaskList($tasklist);

        return $this->render('AppBundle:worklog:tasklist.html.twig', [
            'workLogs' => $workLogs,
        ]);
    }

    /**
     * @Route("/completedTasks", name="completed_tasks")
     */
    public function completedTasksAction(Request $request): ?Response
    {
        $em = $this->getDoctrine()->getManager();
        $tasksRepo = $em->getRepository(Tasks::class);

        $taskListName = $request->get('taskList');
        $accountName = $request->get('account');
        $clientName = $request->get('client');
        $log = is_null($request->get('unlog'));

        $criteria = ['t.completed' => true, 't.workLoggable' => $log];
        $orderBy = ['completedAt' => 'DESC'];
        $limitBy = 500;
        $offset = null;

        if ($taskListName) {
            $criteria['tl.name'] = $taskListName;
        } elseif ($accountName) {
            $criteria['a.name'] = $accountName;
        } elseif ($clientName) {
            $criteria['c.name'] = $clientName;
        }
        $tasks = $tasksRepo->findByWithJoins($criteria, $orderBy, $limitBy, $offset);

        return $this->render('AppBundle:worklog:completedTasks.html.twig', [
            'unlog' => $log,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Creates a new workLog entity.
     *
     * @Route("/new", name="worklog_new", methods={"GET", "POST"})
     * @todo: move to service
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /** Cost Of Life * */
        $currencies = $em->getRepository(Currency::class)->findAll();
        $cost = $em->getRepository(CostOfLife::class)->sumCostOfLife()['cost'];

        $costOfLife = new CostOfLifeLogic($cost, $currencies);
        $pricePerUnit = $costOfLife->getHourly();
        $workLog = new Worklog();

        if ($request->get('task')) {
            $task = $em->getRepository(Tasks::class)->find($request->get('task'));
            if (!$task) {
                throw new NotFoundHttpException();
            }

            $thisRates = $em->getRepository(Rate::class)->findOneBy([
                'active' => true,
                'client' => $task->getTaskList()->getAccount()->getClient(),
            ]);
            if ($thisRates) {
                $pricePerUnit = $thisRates->getRate();
            }

            $billingOption = $task->getAccount()->getClient()->getBillingOption();
            if ($billingOption && $billingOption['amount'] > 0) {
                $billingCalculator = new BillingCalculator($billingOption);
                $pricePerUnit = $billingCalculator->getPricePerUnit();
            }

            $workLog->setPricePerUnit($pricePerUnit);
            $workLog->setTask($task);
            $workLog->setName($task->getTask());
            $workLog->setDuration($task->getDuration());
            $workLog->setTotal($workLog->getPricePerUnit() / 60 * $workLog->getDuration());
        }

        $form = $this->createForm(WorkLogType::class, $workLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $workLog->getTask();
            $accountTransactions = new AccountTransactions();
            $accountTransactions->setNote($workLog->getTask());
            $accountTransactions->setAmount($workLog->getTotal() * -1);
            $accountTransactions->setAccount($task->getAccount());
            $accountTransactions->setIssuedAt($task->getCompletedAt());

            $em->persist($workLog);
            $em->persist($accountTransactions);
            $em->flush();

            return $this->redirectToRoute('worklog_show', ['id' => $workLog->getId()]);
        }
        $clientRates = $em->getRepository(Rate::class)->findBy(['active' => true]);

        return $this->render('AppBundle:worklog:new.html.twig', [
            'workLog' => $workLog,
            'costOfLife' => $costOfLife,
            'form' => $form->createView(),
            'clientRates' => $clientRates,
        ]);
    }

    /**
     * Auto Creates a new workLog entity.
     *
     * @Route("/autolog", name="worklog_autolog", methods={"GET", "POST"})
     * @todo: move to service
     */
    public function autologAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $createNewTransaction = false;

        /** Cost Of Life * */
        $currencies = $em->getRepository(Currency::class)->findAll();
        $cost = $em->getRepository(CostOfLife::class)->sumCostOfLife()['cost'];

        $costOfLife = new CostOfLifeLogic($cost, $currencies);

        $taskIds = $request->get('task_ids');
        foreach ($taskIds as $taskId) {
            $task = $em->getRepository(Tasks::class)->find($taskId);
            if (!$task) {
                throw new NotFoundHttpException();
            }

            if ($task->getDuration() > 0) {
                if (is_null($task->getWorkLog())) {
                    $createNewTransaction = true;
                    $task->setWorkLog(new WorkLog());
                }

                $task->setWorkLoggable(true);
                $task->getWorklog()->setTask($task);
                $pricePerUnit = $costOfLife->getHourly();

                $thisRates = $em->getRepository(Rate::class)->findOneBy([
                    'active' => true,
                    'client' => $task->getTaskList()->getAccount()->getClient(),
                ]);
                if ($thisRates) {
                    $pricePerUnit = $thisRates->getRate();
                }
                $billingOption = $task->getAccount()->getClient()->getBillingOption();
                if ($billingOption && $billingOption['amount'] > 0) {
                    $billingCalculator = new BillingCalculator($billingOption);
                    $pricePerUnit = $billingCalculator->getPricePerUnit();
                }
                $task->getWorkLog()->setPricePerUnit(($pricePerUnit) ?: 0);
                $task->getWorklog()->setName($task->getTask());
                $task->getWorklog()->setDuration($task->getDuration());
                $total = $task->getWorklog()->getPricePerUnit() / 60 * $task->getWorklog()->getDuration();
                $task->getWorklog()->setTotal($total);
                if ($createNewTransaction) {
                    $workLog = $task->getWorklog();
                    $accountTransactions = new AccountTransactions();
                    $accountTransactions->setNote($workLog->getTask());
                    $accountTransactions->setAmount($workLog->getTotal() * -1);
                    $accountTransactions->setAccount($task->getAccount());
                    $accountTransactions->setIssuedAt($task->getCompletedAt());
                    $em->persist($accountTransactions);
                }
                $em->persist($task->getWorklog());
            } else {
                $url = $this->generateUrl('tasks_show', ['id' => $taskId]);
                $this->addFlash(
                    'warning_raw',
                    'Task  <a href="'.$url.'" target="_new">'.$taskId.'</a> has 0 est'
                );
            }
            $em->flush();
        }

        return new Response();
    }

    /**
     * Marks tasks as unloggale.
     *
     * @Route("/unloggable", name="worklog_unloggable", methods={"GET", "POST"})
     */
    public function unloggableAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $taskIds = $request->get('task_ids');
        foreach ($taskIds as $taskId) {
            $task = $em->getRepository(Tasks::class)->find($taskId);
            if (!$task) {
                throw new NotFoundHttpException();
            }

            if ($task->getWorklog()) {
                $worklog = $task->getWorklog();
                $em->remove($worklog);
            }

            $task->setWorklog();
            $task->setWorkLoggable(false);
            $em->flush();
        }

        return new Response();
    }

    /**
     * Deletes a workLog entity.
     *
     * @Route("/autodelete", name="worklog_autodelete", methods={"GET", "POST"})
     */
    public function autodeleteAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $taskIds = $request->get('task_ids');
        foreach ($taskIds as $taskId) {
            $task = $em->getRepository(Tasks::class)->find($taskId);
            if (!$task) {
                throw new NotFoundHttpException();
            }
            if ($task->getWorklog()) {
                $em->remove($task->getWorklog());
            }
        }
        $em->flush();

        return new Response();
    }

    /**
     * Finds and displays a workLog entity.
     *
     * @Route("/{id}", name="worklog_show", methods={"GET"})
     */
    public function showAction(WorkLog $workLog): ?Response
    {
        $deleteForm = $this->createDeleteForm($workLog);

        return $this->render('AppBundle:worklog:show.html.twig', [
            'workLog' => $workLog,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing workLog entity.
     *
     * @Route("/{id}/edit", name="worklog_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, WorkLog $workLog)
    {
        $deleteForm = $this->createDeleteForm($workLog);
        $editForm = $this->createForm(WorkLogType::class, $workLog);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('worklog_edit', ['id' => $workLog->getId()]);
        }

        return $this->render('AppBundle:worklog:edit.html.twig', [
            'workLog' => $workLog,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a workLog entity.
     *
     * @Route("/{id}", name="worklog_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, WorkLog $workLog): RedirectResponse
    {
        $form = $this->createDeleteForm($workLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($workLog);
            $em->flush();
        }

        return $this->redirectToRoute('worklog_index');
    }

    /**
     * Creates a form to delete a workLog entity.
     *
     * @param WorkLog $workLog The workLog entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(WorkLog $workLog): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('worklog_delete', ['id' => $workLog->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
