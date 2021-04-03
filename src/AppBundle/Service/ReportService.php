<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Service;


use AppBundle\Repository\AccountTransactionsRepository;
use stdClass;

class ReportService
{
    /**
     *
     * @var AccountTransactionsRepository $accountTransactionRepository
     */
    protected $accountTransactionRepository;


    /**
     * ReportService constructor.
     * @param AccountTransactionsRepository $accountTransactionRepository
     */
    public function __construct(AccountTransactionsRepository $accountTransactionRepository)
    {
        $this->setAccountTransactionRepository($accountTransactionRepository);
    }

    /**
     * @return AccountTransactionsRepository
     */
    public function getAccountTransactionRepository(): AccountTransactionsRepository
    {
        return $this->accountTransactionRepository;
    }

    /**
     * @param AccountTransactionsRepository $accountTransactionRepository
     */
    public function setAccountTransactionRepository(AccountTransactionsRepository $accountTransactionRepository): void
    {
        $this->accountTransactionRepository = $accountTransactionRepository;
    }

    /**
     * Calculated income per month for entire span of account transactions
     * @return array
     */
    public function getIncome()
    {
        $txns = $this->getAccountTransactionRepository()->queryIncome();

        $income = [];
        foreach ($txns as $txn) {
            if (!array_key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = [];
            }
            if (!array_key_exists($txn->getIssuedAt()->format('m'), $income[$txn->getIssuedAt()->format('Y')])) {
                $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] = 0;
            }
            $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] += $txn->getAmount();
        }
        return $income;
    }

    public function getIncomeGoogleChart()
    {
        $income = $this->getIncome();
        $rows = [];
        foreach ($income as $year => $months) {
            foreach ($months as $month => $amountValue) {
                $issuedAt = new stdClass();
                $issuedAt->v = "Date(" . $year . "," . ($month - 1) . ",1)";
                $amount = new stdClass();
                $amount->v = $amountValue;
                $row = new stdClass();
                $row->c = [$issuedAt, $amount];
                $rows[] = $row;
            }
        }

        $IssuedAtCol = new stdClass();
        $IssuedAtCol->label = "IssuedAt";
        $IssuedAtCol->type = "date";
        $AmountCol = new stdClass();
        $AmountCol->label = "Amount";
        $AmountCol->type = "number";
        $columns = [$IssuedAtCol, $AmountCol];

        return [
            "cols" => $columns,
            "rows" => $rows
        ];
    }
}