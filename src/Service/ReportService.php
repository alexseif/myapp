<?php
/**
 * The following content was designed & implemented under AlexSeif.com.
 **/

namespace App\Service;

use App\Repository\AccountTransactionsRepository;
use App\Repository\TasksRepository;
use stdClass;

class ReportService
{

    /**
     * @var AccountTransactionsRepository
     */
    protected $accountTransactionRepository;

    /**
     * @var TasksRepository
     */
    protected $tasksRepository;

    /**
     * ReportService constructor.
     */
    public function __construct(
      AccountTransactionsRepository $accountTransactionRepository,
      TasksRepository $tasksRepository
    ) {
        $this->setAccountTransactionRepository($accountTransactionRepository);
        $this->setTasksRepository($tasksRepository);
    }

    public function getAccountTransactionRepository(
    ): AccountTransactionsRepository
    {
        return $this->accountTransactionRepository;
    }

    public function setAccountTransactionRepository(
      AccountTransactionsRepository $accountTransactionRepository
    ): void {
        $this->accountTransactionRepository = $accountTransactionRepository;
    }

    public function getTasksRepository(): TasksRepository
    {
        return $this->tasksRepository;
    }

    public function setTasksRepository(TasksRepository $tasksRepository): void
    {
        $this->tasksRepository = $tasksRepository;
    }

    /**
     * Calculated income per month for entire span of account transactions.
     *
     * @return array
     */
    public function getIncome(): array
    {
        $txns = $this->getAccountTransactionRepository()->queryIncome();

        $income = [];
        foreach ($txns as $txn) {
            if (!array_key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = [];
            }
            if (!array_key_exists(
              $txn->getIssuedAt()->format('m'),
              $income[$txn->getIssuedAt()->format('Y')]
            )) {
                $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()
                  ->format('m')] = 0;
            }
            $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()
              ->format('m')] += $txn->getAmount();
        }

        return $income;
    }

    public function getIncomeGoogleChart(): array
    {
        $income = $this->getIncome();
        $rows = [];
        foreach ($income as $year => $months) {
            foreach ($months as $month => $amountValue) {
                $issuedAt = new stdClass();
                $issuedAt->v = 'Date(' . $year . ',' . ($month - 1) . ',1)';
                $amount = new stdClass();
                $amount->v = $amountValue;
                $row = new stdClass();
                $row->c = [$issuedAt, $amount];
                $rows[] = $row;
            }
        }

        $IssuedAtCol = new stdClass();
        $IssuedAtCol->label = 'IssuedAt';
        $IssuedAtCol->type = 'date';
        $AmountCol = new stdClass();
        $AmountCol->label = 'Amount';
        $AmountCol->type = 'number';
        $columns = [$IssuedAtCol, $AmountCol];

        return [
          'cols' => $columns,
          'rows' => $rows,
        ];
    }

    public function getHoursPerMonthGoogleChart(): array
    {
        $hoursPerMonths = $this->getTasksRepository()->findWorkingHoursPerMonth(
        );
        $rows = [];
        foreach ($hoursPerMonths as $hoursPerMonth) {
            $completedAt = new stdClass();
            $completedAt->v = 'Date(' . $hoursPerMonth['completedAt']->format(
                'Y'
              ) . ',' . ($hoursPerMonth['completedAt']->format(
                  'm'
                ) - 1) . ',1)';
            $duration = new stdClass();
            $duration->v = $hoursPerMonth['duration'];
            $row = new stdClass();
            $row->c = [$completedAt, $duration];
            $rows[] = $row;
        }

        $IssuedAtCol = new stdClass();
        $IssuedAtCol->label = 'CompletedAt';
        $IssuedAtCol->type = 'date';
        $AmountCol = new stdClass();
        $AmountCol->label = 'Duration';
        $AmountCol->type = 'number';
        $columns = [$IssuedAtCol, $AmountCol];

        return [
          'cols' => $columns,
          'rows' => $rows,
        ];
    }

}
