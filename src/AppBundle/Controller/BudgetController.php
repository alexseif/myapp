<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Budget controller.
 *
 * @Route("/budget")
 */
class BudgetController extends Controller
{

  /**
   * 
   * @Route("/", name="budget_index")
   * @Template("AppBundle:Budget:index.html.twig")
   */
  public function indexAction()
  {
    $today = new \DateTime();
    // Begining of month
    $startDate = new \DateTime();
    $endDate = new \DateTime();
    $startDate->setDate($today->format('Y'), $today->format('n'), 1);
    $endDate->setDate($today->format('Y'), $today->format('n'), 1);
    // End of month
    $endDate->add(new \DateInterval('P1M'));

    $interval = new \DateInterval('P1D');
    $dateRange = new \DatePeriod($startDate, $interval, $endDate);

    $sheet = array();

    $value = 200;
    $balance = 0;

    foreach ($dateRange as $key => $dateIndex) {
      $dateDiff = $today->diff($dateIndex);
      $sheet[$key]['item'] = "Daily";
      $sheet[$key]['dateIndex'] = $dateIndex;
      $sheet[$key]['days'] = $dateDiff;
      $sheet[$key]['value'] = $value;
      $balance -= $value;
      $sheet[$key]['balance'] = $balance;
    }

    return array(
      'today' => $today,
      'dateRange' => $dateRange,
      'sheet' => $sheet
    );
  }

}
