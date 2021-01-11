<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use AppBundle\Entity\TaskLists;

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
        return $this->render('AppBundle:Reports:index.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/clients", name="reports_clients")
     */
    public function clientsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository('AppBundle:Client')->findAll();

        return $this->render('AppBundle:Reports:clients.html.twig', array(
            "clients" => $clients
        ));
    }

    /**
     * @Route("/hourly/{client}", name="reports_client_hourly")
     * @param Client $client
     * @return type
     */
    public function hourlyAction(Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $durations = $em->getRepository('AppBundle:Tasks')->findCompletedByClient($client);
        $hourly = array();
        $year = 0;
        $month = 0;
        foreach ($durations as $duration) {
            if (!key_exists($duration['yr'], $hourly)) {
                $hourly[$duration['yr']] = array();
            }
            if (!key_exists($duration['mnth'], $hourly[$duration['yr']])) {
                $hourly[$duration['yr']][$duration['mnth']] = 0;
            }
            $hourly[$duration['yr']][$duration['mnth']] += $duration['duration'];
        }
        $yearAverage = 0;
        foreach ($hourly as $year => $hour) {
            $hourly[$year]['average'] = array_sum($hour) / count($hour);
        }

        return $this->render('AppBundle:Reports:hourly.html.twig', array(
            "client" => $client,
            'hourly' => $hourly
        ));
    }

    /**
     * @Route("/tasklist/{tasklist}", name="reports_tasklist")
     */
    public function tasklistAction(TaskLists $tasklist)
    {
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('AppBundle:Tasks')->findBy(array(
            "taskList" => $tasklist,
            "completed" => true
        ), array("completedAt" => "DESC"));

        return $this->render('AppBundle:Reports:tasks.html.twig', array(
            "tasklist" => $tasklist,
            "tasks" => $tasks
        ));
    }

    /**
     *
     * @Route("/diff", name="reports_diff")
     */
    public function diffAction()
    {
        $em = $this->getDoctrine()->getManager();
        $txns = $em->getRepository('AppBundle:AccountTransactions')->queryIncome();

        $income = [];
        $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        foreach ($txns as $txn) {
            if (!key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = [];
                foreach ($months as $month) {
                    $income[$txn->getIssuedAt()->format('Y')][$month] = 0;
                }
            }
            $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] += $txn->getAmount();
        }

        return $this->render('AppBundle:Reports:diff.html.twig', array(
            "income" => $income
        ));
    }

    /**
     *
     * @Route("/income", name="reports_income")
     */
    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $txns = $em->getRepository('AppBundle:AccountTransactions')->queryIncome();

        $income = [];
        foreach ($txns as $txn) {
            if (!key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = [];
            }
            if (!key_exists($txn->getIssuedAt()->format('m'), $income[$txn->getIssuedAt()->format('Y')])) {
                $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] = 0;
            }
            $income[$txn->getIssuedAt()->format('Y')][$txn->getIssuedAt()->format('m')] += $txn->getAmount();
        }

        return $this->render('AppBundle:Reports:income.html.twig', array(
            "income" => $income
        ));
    }

    /**
     *
     * @Route("/annual_income", name="reports_annual_income")
     */
    public function annualIncomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $txns = $em->getRepository('AppBundle:AccountTransactions')->queryIncome();

        $income = [];
        foreach ($txns as $txn) {
            if (!key_exists($txn->getIssuedAt()->format('Y'), $income)) {
                $income[$txn->getIssuedAt()->format('Y')] = 0;
            }
            $income[$txn->getIssuedAt()->format('Y')] += $txn->getAmount();
        }

        return $this->render('AppBundle:Reports:annual_income.html.twig', array(
            "income" => $income
        ));
    }

    /**
     * @Route("/tasks_years", name="reports_tasks_years")
     */
    public function tasksYearsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tasksYears = $em->getRepository('AppBundle:Tasks')->findTasksYears();
        return $this->render('AppBundle:Reports:tasksYears.html.twig', array(
            "tasksYears" => $tasksYears
        ));
    }

    /**
     * @Route("/clients_by_year/{year}", name="reports_clients_by_years")
     */
    public function clientsByYearAction($year)
    {
        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository('AppBundle:Client')->findByYear($year);
        $reports = [];

        foreach ($clients as $client) {
            $accounts = $em->getRepository('AppBundle:Accounts')
                ->findByYearAndClient($year, $client);
            $report = [
                "client" => $client,
                "accounts" => $accounts,
            ];
            $reports[] = $report;
        }

        return $this->render('AppBundle:Reports:clientsByYear.html.twig', array(
            "reports" => $reports
        ));
    }

}
