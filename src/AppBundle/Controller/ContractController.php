<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountTransactions;
use AppBundle\Entity\Contract;
use AppBundle\Entity\Holiday;
use AppBundle\Entity\Tasks;
use AppBundle\Form\ContractType;
use AppBundle\Util\DateRanges;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contract controller.
 *
 * @Route("contract")
 */
class ContractController extends Controller
{
    /**
     * Lists all contract entities.
     *
     * @Route("/", name="contract_index", methods={"GET"})
     */
    public function indexAction(): ?Response
    {
        $em = $this->getDoctrine()->getManager();

        $contracts = $em->getRepository(Contract::class)->findAll();

        return $this->render('@App/contract/index.html.twig', [
            'contracts' => $contracts,
        ]);
    }

    /**
     * Creates a new contract entity.
     *
     * @Route("/new", name="contract_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $contract = new Contract();
        $form = $this->createForm(ContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contract);
            $em->flush();

            return $this->redirectToRoute('contract_show', ['id' => $contract->getId()]);
        }

        return $this->render('@App/contract/new.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a contract entity.
     *
     * @Route("/{id}", name="contract_show", methods={"GET"})
     */
    public function showAction(Contract $contract): ?Response
    {
        $deleteForm = $this->createDeleteForm($contract);

        return $this->render('@App/contract/show.html.twig', [
            'contract' => $contract,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Finds and displays a report for contract entity.
     *
     * @Route("/{id}/log-summary", name="contract_log_summary", methods={"GET"})
     */
    public function logSummaryAction(Contract $contract): ?Response
    {
        $today = new DateTime();
        $day = $contract->getBilledOn() ?: 25;

        $months = DateRanges::populateMonths($contract->getStartedAt()->format('Ymd'), $today->format('Ymd'), $day);

        return $this->render('@App/contract/log-summary.html.twig', [
            'contract' => $contract,
            'months' => $months,
        ]);
    }

    /**
     * Finds and displays a report for contract entity.
     *
     * @Route("/{id}/log/{from}/{to}", name="contract_log", methods={"GET"})
     *
     * @throws Exception
     */
    public function logAction(Contract $contract, $from, $to): ?Response
    {
        $em = $this->getDoctrine()->getManager();
        $tasksRepo = $em->getRepository(Tasks::class);
        $fromDate = new DateTime($from);
        $fromDate->setTime(0, 0, 0);
        $toDate = new DateTime($to);
        $toDate->setTime(23, 23, 59);
        $workweek = [1, 2, 3, 4, 7];

        $dayInterval = new DateInterval('P1D');

        $contractDays = new DatePeriod($fromDate, $dayInterval, $toDate);

        $holidays = [];
        $contractDetails = [];
        $workingDays = 22;
        $expected = ($workingDays * $contract->getHoursPerDay());
        $total = 0;
        foreach ($contractDays as $day) {
            if (in_array($day->format('N'), $workweek)) {
                $holidays = $em->getRepository(Holiday::class)->findByRange($day, $day);
                if ($holidays) {
                    $total += $contract->getHoursPerDay() * 60;
                    continue;
                }

                $dayKey = (int)$day->format('Ymd');
                if (!array_key_exists($dayKey, $contractDetails)) {
                    $contractDetails[$dayKey] = [];
                }
                $contractDetails[$dayKey]['title'] = $day->format('D Y-m-d');
                $contractDetails[$dayKey]['day'] = $day;
                $contractDetails[$dayKey]['tasks'] = $tasksRepo->findCompletedByClientByDate($contract->getClient(),
                    $day);
                $contractDetails[$dayKey]['total'] = $tasksRepo->sumDurationByClientByDate($contract->getClient(),
                    $day);
                $total += $contractDetails[$dayKey]['total']['duration'];
            }
        }
        ksort($contractDetails);
        $totalHours = $total / 60;
        $totalMins = $total % 60;
        $remaining = $expected - $totalHours;
        $sign = ($remaining < 0);
        $remaining = (($sign) ? "+" : "-") . floor(abs($remaining)) . ':' . $totalMins;
        return $this->render('@App/contract/log.html.twig', [
            'contract' => $contract,
            'from' => $from,
            'to' => $to,
            'contractDetails' => $contractDetails,
            'holidays' => $holidays,
            'expected' => $expected,
            'total' => $total,
            'remaining' => $remaining
        ]);
    }

    /**
     * @Route("/{id}/report", name="contract_report")
     */
    public function reportAction(Contract $contract, Request $request): ?Response
    {
        $reportFilterFormBuider = $this->createFormBuilder()
            ->add('test');
        $reportFilterForm = $reportFilterFormBuider->getForm();

        $reportFilterForm->handleRequest($request);

        $day = $contract->getBilledOn() ?: 25;

        $today = new DateTime();
        $monthsArray = DateRanges::populateMonths($contract->getStartedAt()->format('Ymd'), $today->format('Ymd'),
            $day);

        if ($reportFilterForm->isSubmitted() && $reportFilterForm->isValid()) {
            $accountingFilterData = $reportFilterForm->getData();
            $contract = $accountingFilterData['account'];

            $em = $this->getDoctrine()->getManager();
            $txnRepo = $em->getRepository(AccountTransactions::class);

            foreach ($monthsArray as $key => $range) {
                $monthsArray[$key]['forward_balance'] = $txnRepo->queryCurrentBalanceByAccountAndRange($contract,
                    $range)['amount'];
                $monthsArray[$key]['ending_balance'] = $txnRepo->queryOverdueAccountTo($contract,
                    $range['end'])['amount'];
            }
        }

        return $this->render('@App/contract/report.html.twig', [
            'report_filter_form' => $reportFilterForm->createView(),
            'contract' => $contract,
            'monthsArray' => $monthsArray,
        ]);
    }

    /**
     * @Route("/{id}/{from}/{to}", name="contract_timesheet")
     *
     * @throws Exception
     */
    public function timesheetAction(Contract $contract, $from, $to): ?Response
    {
        $em = $this->getDoctrine()->getManager();
        $fromDate = new DateTime($from);
        $fromDate->setTime(0, 0, 0);
        $toDate = new DateTime($to);
        $toDate->setTime(23, 23, 59);
        $tasks = $em->getRepository(Tasks::class)->findCompletedByClientByRange($contract->getClient(), $fromDate,
            $toDate);
        $workingDays = 22;
        $expected = ($workingDays * $contract->getHoursPerDay());
        $total = 0;


        foreach ($tasks as $task) {
            $total += $task->getDuration();
        }
        $totalHours = $total / 60;
        $totalMins = $total % 60;
        $remaining = $expected - $totalHours;
        $sign = ($remaining < 0);
        $remaining = (($sign) ? "+" : "-") . floor(abs($remaining)) . ':' . $totalMins;

        return $this->render('@App/contract/timesheet.html.twig', [
            'contract' => $contract,
            'from' => $from,
            'to' => $to,
            'tasks' => $tasks,
            'total' => $total,
            'expected' => $expected,
            'remaining' => $remaining,
        ]);
    }

    /**
     * Displays a form to edit an existing contract entity.
     *
     * @Route("/{id}/edit", name="contract_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Contract $contract)
    {
        $deleteForm = $this->createDeleteForm($contract);
        $editForm = $this->createForm(ContractType::class, $contract);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($contract->getIsCompleted()) {
                if (is_null($contract->getCompletedAt())) {
                    $contract->setCompletedAt(new DateTime());
                }
            } else {
                $contract->setCompletedAt(null);
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Contract updated');

            return $this->redirectToRoute('contract_index');
        }

        return $this->render('@App/contract/edit.html.twig', [
            'contract' => $contract,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a contract entity.
     *
     * @Route("/{id}", name="contract_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Contract $contract): RedirectResponse
    {
        $form = $this->createDeleteForm($contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contract);
            $em->flush();
        }

        return $this->redirectToRoute('contract_index');
    }

    /**
     * Creates a form to delete a contract entity.
     *
     * @param Contract $contract The contract entity
     */
    private function createDeleteForm(Contract $contract): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contract_delete', ['id' => $contract->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
