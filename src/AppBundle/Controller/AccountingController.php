<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Form\AccountingMainFilterType;
use AppBundle\Entity\Accounts;

/**
 * Accounting controller.
 *
 * @Route("/accounting")
 */
class AccountingController extends Controller
{

  /**
   * @Route("/", name="accounting_index", methods={"GET"})
   */
  public function indexAction(Request $request)
  {
    $accountingFilterForm = $this->getFilterFunction($request);

    return $this->render('AppBundle:Accounting:index.html.twig', array(
          'accounting_filter_form' => $accountingFilterForm->createView()
    ));
  }

  /**
   * @Route("/account", name="accounting_account_page", methods={"GET"})
   * @Template("AppBundle:Accounting:account.html.twig")
   */
  public function accountAction(Request $request)
  {
    $accountingFilterForm = $this->getFilterFunction($request);

    $accountingFilterForm->handleRequest($request);

    if ($accountingFilterForm->isSubmitted() && $accountingFilterForm->isValid()) {
      $accountingFilterData = $accountingFilterForm->getData();
      $account = $accountingFilterData['account'];

      $em = $this->getDoctrine()->getManager();
      $txnRepo = $em->getRepository('AppBundle:AccountTransactions');
      $txnPeriod = $txnRepo->queryAccountRange($account);

      $monthsArray = \AppBundle\Util\DateRanges::populateMonths($txnPeriod['rangeStart'], $txnPeriod['rangeEnd'], 1);
    }

    return array(
      'accounting_filter_form' => $accountingFilterForm->createView(),
      'account' => $account,
      'txnPeriod' => $txnPeriod,
      'monthsArray' => $monthsArray
    );
  }

  /**
   * @Route("/balance/{accountId}/{from}/{to}", name="accounting_balance_page", methods={"GET"})
   * @Template("AppBundle:Accounting:balance.html.twig")
   */
  public function balanceAction(Request $request, $accountId, $from, $to, $taxes = false)
  {
    $em = $this->getDoctrine()->getManager();
    $account = $em->getRepository('AppBundle:Accounts')->find($accountId);
    $txnRepo = $em->getRepository('AppBundle:AccountTransactions');
    $txns = $txnRepo->queryAccountFromTo($account, $from, $to);
    $currentBalance = $txnRepo->queryCurrentBalanceByAccount($account);
    $overdue = $txnRepo->queryOverdueAccountTo($account, $from);
    $taxes = null;
    $total = 0;

    foreach ($txns as $txn) {
      $total += $txn->getAmount();
    }

    return array(
    "account" => $account,
    "from" => $from,
    "to" => $to,
    "txns" => $txns,
    "total" => $total,
    "currentBalance" => $currentBalance,
    "overdue" => $overdue,
    "taxes" => $taxes
    );
  }

  /**
   * @Route("/statements", name="accounting_statements_page", methods={"GET"})
   */
  public function statementAction(Request $request)
  {
    $accountingFilterForm = $this->getFilterFunction($request);

    $accountingFilterForm->handleRequest($request);

    if ($accountingFilterForm->isSubmitted() && $accountingFilterForm->isValid()) {
      // TODO: create statements
      $accountingFilterData = $accountingFilterForm->getData();
      $account = $accountingFilterData['account'];

      $em = $this->getDoctrine()->getManager();
      $txnRepo = $em->getRepository('AppBundle:AccountTransactions');
      $txnPeriod = $txnRepo->queryAccountRange($account);

      $begin = new \DateTime($txnPeriod['rangeStart']);
      $begin->setDate($begin->format("Y"), $begin->format("m"), 1);
      $end = new \DateTime($txnPeriod['rangeEnd']);
      $interval = \DateInterval::createFromDateString('1 month');
      $period = new \DatePeriod($begin, $interval, $end);
      $dateArray = array();

      foreach ($period as $dt) {
        $dateArray[] = $dt;
      }
    }

    return $this->render('AppBundle:Accounting:account.html.twig', array(
          'accounting_filter_form' => $accountingFilterForm->createView(),
          'account' => $account,
          'txnPeriod' => $txnPeriod,
          'monthsArray' => $dateArray
    ));
  }

  protected function getFilterFunction(Request $request)
  {
    return $this->createForm(AccountingMainFilterType::class, $request->get('accounting_filter'), array(
          'method' => 'GET',
          'action' => $this->generateUrl('accounting_account_page')
    ));
  }

  public function nb_mois($date1, $date2)
  {
    $begin = new \DateTime($date1);
    $end = new \DateTime($date2);
    $end = $end->modify('+1 month');

    $interval = \DateInterval::createFromDateString('1 month');

    $period = new \DatePeriod($begin, $interval, $end);
    $counter = 0;
    foreach ($period as $dt) {
      $counter++;
    }

    return $counter;
  }

}
