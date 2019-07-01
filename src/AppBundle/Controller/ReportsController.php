<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;

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

}
