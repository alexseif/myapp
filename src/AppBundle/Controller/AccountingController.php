<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\AccountTransactions;
use AppBundle\Form\AccountingMainFilterType;
use AppBundle\Util\DateRanges;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $em = $this->getDoctrine()->getManager();
        $accounts = $em->getRepository(Accounts::class)->findBy(['conceal' => false]);

        return $this->render('Accounting/index.html.twig', [
            'accounts' => $accounts,
            'accounting_filter_form' => $accountingFilterForm->createView(),
        ]);
    }

    /**
     * @Route("/account/{id}", name="accounting_account_page", methods={"GET"})
     */
    public function accountAction(Request $request, Accounts $account)
    {
        $accountingFilterForm = $this->getFilterFunction($request);

        $em = $this->getDoctrine()->getManager();
        $txnRepo = $em->getRepository(AccountTransactions::class);
        $balance = [
            'current' => 0,
            'overdue' => 0,
            'invoices' => [],
            'period' => $txnRepo->queryAccountRange($account),
        ];
        if ($balance['period']) {
            $balance['current'] = $txnRepo->queryCurrentBalanceByAccount($account)['amount'];
            $balance['overdue'] = $txnRepo->queryOverdueAccount($account)['amount'];
            $balance['invoices'] = DateRanges::populateMonths($balance['period']['rangeStart'],
                $balance['period']['rangeEnd'], 25);
            foreach ($balance['invoices'] as $key => $range) {
                $balance['invoices'][$key]['forward_balance'] = $txnRepo
                    ->queryCurrentBalanceByAccountAndRange($account, $range)['amount'];
                $balance['invoices'][$key]['ending_balance'] = $txnRepo
                    ->queryOverdueAccountTo($account, $range['end'])['amount'];
            }
        }

        return $this->render('Accounting/account.html.twig', [
            'accounting_filter_form' => $accountingFilterForm->createView(),
            'account' => $account,
            'balance' => $balance,
            'txnPeriod' => $balance['period'],
        ]);
    }

    /**
     * @Route("/balance/{id}/{from}/{to}", name="accounting_balance_page", methods={"GET"})
     */
    public function balanceAction(Request $request, Accounts $account, $from, $to, $taxes = false)
    {
        $em = $this->getDoctrine()->getManager();
        $txnRepo = $em->getRepository(AccountTransactions::class);
        $txns = $txnRepo->queryAccountFromTo($account, $from, $to);
        $balanceTo = $txnRepo->queryCurrentBalanceByAccountTo($account, $to);
        $currentBalance = $txnRepo->queryCurrentBalanceByAccount($account, $to);
        $overdue = $txnRepo->queryOverdueAccountTo($account, $from);
        $total = $txnRepo->queryAmountFromTo($account, $from, $to);

        return $this->render('Accounting/balance.html.twig', [
            'account' => $account,
            'from' => $from,
            'to' => $to,
            'txns' => $txns,
            'total' => $total,
            'balanceTo' => $balanceTo,
            'currentBalance' => $currentBalance,
            'overdue' => $overdue,
            'taxes' => $taxes,
        ]);
    }

    protected function getFilterFunction(Request $request)
    {
        return $this->createForm(AccountingMainFilterType::class, $request->get('accounting_filter'), [
            'method' => 'GET',
        ]);
    }
}
