<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contract;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $contracts = $em->getRepository('AppBundle:Contract')->findAll();

    return $this->render('AppBundle:contract:index.html.twig', [
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
    $form = $this->createForm('AppBundle\Form\ContractType', $contract);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($contract);
      $em->flush();

      return $this->redirectToRoute('contract_show', ['id' => $contract->getId()]);
    }

    return $this->render('AppBundle:contract:new.html.twig', [
          'contract' => $contract,
          'form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a contract entity.
   *
   * @Route("/{id}", name="contract_show", methods={"GET"})
   */
  public function showAction(Contract $contract)
  {
    $deleteForm = $this->createDeleteForm($contract);

    return $this->render('AppBundle:contract:show.html.twig', [
          'contract' => $contract,
          'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Finds and displays a report for contract entity.
   *
   * @Route("/{id}/log", name="contract_log", methods={"GET"})
   */
  public function logAction(Contract $contract)
  {
    $em = $this->getDoctrine()->getManager();

    $contractStart = $contract->getStartedAt()
        ->setDate($contract->getStartedAt()->format("Y"), $contract->getStartedAt()->format("m"), 25)
        ->modify("-1 month");
    $today = new \DateTime();
    $workweek = [1, 2, 3, 4, 7];

    $dayInterval = new \DateInterval("P1D");
    $monthInterval = new \DateInterval("P1M");

    $contractMonths = new \DatePeriod($contractStart, $monthInterval, $today);

    $holidays = [];
    $contractDetails = [];
    $totals = [];
    foreach ($contractMonths as $month) {
      $from = new \DateTime();
      $from->setDate($month->format('Y'), $month->format('m'), 25)
          ->modify("-1 month")
          ->setTime(00, 00, 00);
      $to = clone $from;
      $to->modify("+1 month")
          ->setTime(23, 59, 59);
      $tasks = $em->getRepository('AppBundle:Tasks')->findCompletedByClientByRange($contract->getClient(), $from, $to);
      $monthKey = (int) $month->format('Ym');
      foreach ($tasks as $task) {
        $day = (int) $task->getCompletedAt()->format('Ymd');
        if (!key_exists($monthKey, $totals)) {
          $totals[$monthKey] = [];
        }
        if (!key_exists('sum', $totals[$monthKey])) {
          $totals[$monthKey]['sum'] = 0;
        }
        if (!key_exists($day, $totals[$monthKey])) {
          $totals[$monthKey][$day] = 0;
        }
        $totals[$monthKey][$day] += $task->getDuration();
        $totals[$monthKey]['sum'] += $totals[$monthKey][$day];
        $contractDetails[$monthKey][$day][] = $task;
      }
      $holidays[$monthKey] = [];
      $contractPeriod = new \DatePeriod($from, $dayInterval, $to);

      foreach ($contractPeriod as $date) {
        if (in_array($date->format('N'), $workweek)) {
          $day = (int) $date->format('Ymd');
          $holiday = $em->getRepository('AppBundle:Holiday')->findOneBy(['date' => $date]);
          if ($holiday) {
            $contractDetails[$monthKey][$day] = $holiday;
            $holidays[$monthKey][$day] = $holiday->getName();
            if (!key_exists($day, $totals[$monthKey])) {
              $totals[$monthKey][$day] = 240;
              $totals[$monthKey]['sum'] += 240;
            }
          }
          ksort($contractDetails[$monthKey]);
        }
      }
    }
    ksort($contractDetails);
    return $this->render('AppBundle:contract:log.html.twig', [
          'contract' => $contract,
          'contractDetails' => $contractDetails,
          'holidays' => $holidays,
          'totals' => $totals
    ]);
  }

  /**
   * @Route("/{id}/report", name="contract_report")
   */
  public function reportAction(Contract $contract, Request $request)
  {
    $reportFilterFormBuider = $this->createFormBuilder()
        ->add('test');
    $reportFilterForm = $reportFilterFormBuider->getForm();

    $reportFilterForm->handleRequest($request);
    $monthsArray = [];
    $today = new \DateTime();
    $monthsArray = \AppBundle\Util\DateRanges::populateMonths($contract->getStartedAt()->format('Ymd'), $today->format('Ymd'), 25);

    if ($reportFilterForm->isSubmitted() && $reportFilterForm->isValid()) {
      $accountingFilterData = $reportFilterForm->getData();
      $contract = $accountingFilterData['account'];

      $em = $this->getDoctrine()->getManager();
      $txnRepo = $em->getRepository('AppBundle:AccountTransactions');
      $txnPeriod = $txnRepo->queryAccountRange($contract);

      $overdue = $txnRepo->queryOverdueAccount($contract);

      foreach ($monthsArray as $key => $range) {
        $monthsArray[$key]['forward_balance'] = $txnRepo->queryCurrentBalanceByAccountAndRange($contract, $range)['amount'];
        $monthsArray[$key]['ending_balance'] = $txnRepo->queryOverdueAccountTo($contract, $range['end'])['amount'];
      }
    }

    return $this->render("AppBundle:contract:report.html.twig", [
          'report_filter_form' => $reportFilterForm->createView(),
          'contract' => $contract,
          'monthsArray' => $monthsArray
    ]);
  }

  /**
   * @Route("/{id}/{from}/{to}", name="contract_timesheet")
   */
  public function timesheetAction(Contract $contract, Request $request, $from, $to)
  {
    $em = $this->getDoctrine()->getManager();
    $fromDate = new \DateTime($from);
    $fromDate->setTime(0, 0, 0);
    $toDate = new \DateTime($to);
    $toDate->setTime(23, 23, 59);
    $tasks = $em->getRepository('AppBundle:Tasks')->findCompletedByClientByRange($contract->getClient(), $fromDate, $toDate);
    $monthHolidays = $em->getRepository('AppBundle:Holiday')->findByRange(new \DateTime($from), new \DateTime($to));
    $workingDays = \AppBundle\Util\DateRanges::getWorkingDays($from, $to);
    $expected = ($workingDays * 4);
    $total = 0;

    $workweek = [1, 2, 3, 4, 7];
    $holidays = [];
    foreach ($monthHolidays as $holiday) {
      if (in_array($holiday->getDate()->format('N'), $workweek)) {
        $holidays[] = $holiday;
        $total += 240;
      }
    }


    foreach ($tasks as $task) {
      $total += $task->getDuration();
    }
    $totalHours = $total / 60;
    $totalMins = $total % 60;
    $remaining = $expected - $totalHours;
    $remaining = floor($remaining) . ":" . $totalMins;

    return $this->render("AppBundle:contract:timesheet.html.twig", [
          "contract" => $contract,
          "from" => $from,
          "to" => $to,
          "tasks" => $tasks,
          "total" => $total,
          "expected" => $expected,
          "remaining" => $remaining,
          "holidays" => $holidays
    ]);
  }

  /**
   * Displays a form to edit an existing contract entity.
   *
   * @Route("/{id}/edit", name="contract_edit", methods={"GET"})
   */
  public function editAction(Request $request, Contract $contract)
  {
    $deleteForm = $this->createDeleteForm($contract);
    $editForm = $this->createForm('AppBundle\Form\ContractType', $contract);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('contract_edit', ['id' => $contract->getId()]);
    }

    return $this->render('AppBundle:contract:edit.html.twig', [
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
  public function deleteAction(Request $request, Contract $contract)
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
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Contract $contract)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('contract_delete', ['id' => $contract->getId()]))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
