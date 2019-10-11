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
    return $this->render('AppBundle:Reports:index.html.twig', array(
            // ...
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
    $ests = $em->getRepository('AppBundle:Tasks')->findCompletedByClient($client);
    $hourly = array();
    $year = 0;
    $month = 0;
    foreach ($ests as $est) {
      if (!key_exists($est['yr'], $hourly)) {
        $hourly[$est['yr']] = array();
      }
      if (!key_exists($est['mnth'], $hourly[$est['yr']])) {
        $hourly[$est['yr']][$est['mnth']] = 0;
      }
      $hourly[$est['yr']][$est['mnth']] += $est['est'];
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
   * @Route("/income", name="reports_income")
   */
  public function incomeAction()
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

    return $this->render('AppBundle:Reports:income.html.twig', array(
          "income" => $income
    ));
  }

}
