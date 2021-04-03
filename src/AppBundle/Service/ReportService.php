<?php
/**
 * The following content was designed & implemented under AlexSeif.com
 **/

namespace AppBundle\Service;


use AppBundle\Repository\AccountTransactionsRepository;
use AppBundle\Repository\TasksRepository;
use stdClass;

class ReportService
{
    /**
     *
     * @var AccountTransactionsRepository $accountTransactionRepository
     */
    protected $accountTransactionRepository;
    /**
     * @var TasksRepository $tasksRepository
     */
    protected $tasksRepository;


    /**
     * ReportService constructor.
     * @param AccountTransactionsRepository $accountTransactionRepository
     * @param TasksRepository $tasksRepository
     */
    public function __construct(AccountTransactionsRepository $accountTransactionRepository, TasksRepository $tasksRepository)
    {
        $this->setAccountTransactionRepository($accountTransactionRepository);
        $this->setTasksRepository($tasksRepository);
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
     * @return TasksRepository
     */
    public function getTasksRepository(): TasksRepository
    {
        return $this->tasksRepository;
    }

    /**
     * @param TasksRepository $tasksRepository
     */
    public function setTasksRepository(TasksRepository $tasksRepository): void
    {
        $this->tasksRepository = $tasksRepository;
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


    public function getHoursPerMonthGoogleChart()
    {
        $hoursPerMonths = $this->getTasksRepository()->findWorkingHoursPerMonth();
        $rows = [];
        foreach ($hoursPerMonths as $hoursPerMonth) {
            $completedAt = new stdClass();
            $completedAt->v = "Date(" . $hoursPerMonth['completedAt']->format('Y') . "," . ($hoursPerMonth['completedAt']->format("m") - 1) . ",1)";
            $duration = new stdClass();
            $duration->v = $hoursPerMonth['duration'];
            $row = new stdClass();
            $row->c = [$completedAt, $duration];
            $rows[] = $row;
        }

        $IssuedAtCol = new stdClass();
        $IssuedAtCol->label = "CompletedAt";
        $IssuedAtCol->type = "date";
        $AmountCol = new stdClass();
        $AmountCol->label = "Duration";
        $AmountCol->type = "number";
        $columns = [$IssuedAtCol, $AmountCol];

        return [
            "cols" => $columns,
            "rows" => $rows
        ];
    }
}