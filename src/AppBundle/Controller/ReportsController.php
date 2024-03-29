<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Accounts;
use AppBundle\Entity\AccountTransactions;
use AppBundle\Entity\Client;
use AppBundle\Entity\TaskLists;
use AppBundle\Entity\Tasks;
use AppBundle\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Rate controller.
 *
 * @Route("reports")
 */
class ReportsController extends Controller
{
    /**
     * @Route("/", name="reports_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Reports:index.html.twig', [// ...
        ]);
    }

    /**
     * @Route("/clients", name="reports_clients")
     */
    public function clientsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository(Client::class)->findAll();

        return $this->render('AppBundle:Reports:clients.html.twig', [
            'clients' => $clients,
        ]);
    }

    /**
     * @Route("/hourly/{client}", name="reports_client_hourly")
     */
    public function hourlyAction(Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $durations = $em->getRepository(Tasks::class)->findCompletedByClient($client);
        $hourly = [];
        foreach ($durations as $duration) {
            if (!array_key_exists($duration['yr'], $hourly)) {
                $hourly[$duration['yr']] = [];
            }
            if (!array_key_exists($duration['mnth'], $hourly[$duration['yr']])) {
                $hourly[$duration['yr']][$duration['mnth']] = 0;
            }
            $hourly[$duration['yr']][$duration['mnth']] += $duration['duration'];
        }
        foreach ($hourly as $year => $hour) {
            $hourly[$year]['average'] = array_sum($hour) / count($hour);
        }

        return $this->render('AppBundle:Reports:hourly.html.twig', [
            'client' => $client,
            'hourly' => $hourly,
        ]);
    }

    /**
     * @Route("/tasklist/{tasklist}", name="reports_tasklist")
     */
    public function tasklistAction(TaskLists $tasklist)
    {
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository(Tasks::class)->findBy([
            'taskList' => $tasklist,
            'completed' => true,
        ], ['completedAt' => 'DESC']);

        return $this->render('AppBundle:Reports:tasks.html.twig', [
            'tasklist' => $tasklist,
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/diff", name="reports_diff")
     */
    public function diffAction()
    {
        $em = $this->getDoctrine()->getManager();
        $txns = $em->getRepository(AccountTransactions::class)->queryIncome();

        $income = [];
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        foreach ($txns as $txn) {
            if (!array_key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = [];
                foreach ($months as $month) {
                    $income[$txn->getIssuedAt()->format('Y')][$month] = 0;
                }
            }
            $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] += $txn->getAmount();
        }

        return $this->render('AppBundle:Reports:diff.html.twig', [
            'income' => $income,
        ]);
    }

    /**
     * @Route("/income", name="reports_income")
     */
    public function incomeAction(ReportService $reportService)
    {
        return $this->render('AppBundle:Reports:income.html.twig', [
            'income' => $reportService->getIncome(),
        ]);
    }

    /**
     * @Route("/income/data", name="reports_income_data")
     */
    public function incomeDataAction(ReportService $reportService)
    {
        return JsonResponse::create($reportService->getIncomeGoogleChart());
    }

    /**
     * @Route("/annual_income", name="reports_annual_income")
     */
    public function annualIncomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $txns = $em->getRepository(AccountTransactions::class)->queryIncome();

        $income = [];
        foreach ($txns as $txn) {
            if (!array_key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = 0;
            }
            $income[$txn->getIssuedAt()->format('Y')] += $txn->getAmount();
        }

        return $this->render('AppBundle:Reports:annual_income.html.twig', [
            'income' => $income,
        ]);
    }

    /**
     * @Route("/tasks_years", name="reports_tasks_years")
     */
    public function tasksYearsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tasksYears = $em->getRepository(Tasks::class)->findTasksYears();

        return $this->render('AppBundle:Reports:tasksYears.html.twig', [
            'tasksYears' => $tasksYears,
        ]);
    }

    /**
     * @Route("/hours_per_month", name="reports_hours_per_month")
     */
    public function hoursPerMonthAction()
    {
        return $this->render('AppBundle:Reports:hoursPerMonth.html.twig');
    }

    /**
     * @Route("/hours_per_month/data", name="reports_hours_per_month_data")
     */
    public function hoursPerMonthDataAction(ReportService $reportService)
    {
        return JsonResponse::create($reportService->getHoursPerMonthGoogleChart());
    }

    /**
     * @Route("/clients_by_year/{year}", name="reports_clients_by_years")
     */
    public function clientsByYearAction($year)
    {
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository(Client::class)->findByYear($year);
        $reports = [];

        foreach ($clients as $client) {
            $accounts = $em->getRepository(Accounts::class)
                ->findByYearAndClient($year, $client);
            $report = [
                'client' => $client,
                'accounts' => $accounts,
            ];
            $reports[] = $report;
        }

        return $this->render('AppBundle:Reports:clientsByYear.html.twig', [
            'reports' => $reports,
        ]);
    }
}
