<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

  /**
   * 
   * @Route("/", name="dashboard")
   */
  public function dashboardAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $taskLists = $em->getRepository('AppBundle:DashboardTaskLists')->findAll();
    $unlistedTasks = $em->getRepository('AppBundle:Tasks')->findUnlisted();
    $randomTasks = $em->getRepository('AppBundle:Tasks')->randomTasks();
    $days = $em->getRepository('AppBundle:Days')->getActiveCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    $tsksCntDay = $em->getRepository('AppBundle:Tasks')->findTasksCountByDay();
    $durationSum = $em->getRepository('AppBundle:Tasks')->sumDuration()["duration"];
    $today = new \DateTime();
    $endDay = new \DateTime();
    $endDay->add(\DateInterval::createFromDateString($durationSum . " minutes"));
    $interval = $endDay->diff($today, true);
    /** Cost Of Life * */

    $costOfLife = $this->get('myapp.cost');

    $countByUrgenctAndPriority = $em->getRepository('AppBundle:Tasks')->countByUrgenctAndPriority();
    $piechart = [];
    $piechart['Urgent & Important'] = 0;
    $piechart['Urgent'] = 0;
    $piechart['Important'] = 0;
    $piechart['Normal'] = 0;
    $piechart['Low'] = 0;

    foreach ($countByUrgenctAndPriority as $key => $row) {
      $row['duration'] = (int) $row['duration'];
      if ($row['urgency']) {
        if ($row['priority']) {
          $piechart['Urgent & Important'] = $row['duration'];
        } else {
          $piechart['Urgent'] = $row['duration'];
        }
      } elseif ($row['priority'] > 0) {
        $piechart['Important'] = $row['duration'];
      } elseif ($row['priority'] < 0) {
        $piechart['Low'] = $row['duration'];
      } else {
        $piechart['Normal'] = $row['duration'];
      }
    }

    $tskCnt = array();
    foreach ($tsksCntDay as $t) {
      $tskCnt[$t['day_name']] = $t['cnt'];
    }

    $earnedLogic = new \AppBundle\Logic\EarnedLogic($em, $costOfLife);
    $earned = $earnedLogic->getEarned();
    return $this->render("AppBundle:Dashboard:dashboard.html.twig", array(
          'taskLists' => $taskLists,
          'randomTasks' => $randomTasks,
          'unlistedTasks' => $unlistedTasks,
          'days' => $days,
          'accounts' => $accounts,
          'earned' => $earned,
          'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
          'tskCnt' => $tskCnt,
          'interval' => $interval,
          'costOfLife' => $costOfLife,
          'piechart' => $piechart,
    ));
  }

  /**
   * 
   * @Route("/workarea", name="workarea")
   */
  public function workareaAction()
  {
    $em = $this->getDoctrine()->getManager();
    $focusTasks = $em->getRepository('AppBundle:Tasks')->focusLimitList();
    $days = $em->getRepository('AppBundle:Days')->getImportantCards();
    $accounts = $em->getRepository('AppBundle:Accounts')->findBy(array('conceal' => false));
    /** Cost Of Life * */
    $costOfLife = $this->get('myapp.cost');
    $earnedLogic = new \AppBundle\Logic\EarnedLogic($em, $costOfLife);
    $earned = $earnedLogic->getEarned();

    $issuedThisMonth = $em->getRepository('AppBundle:AccountTransactions')->issuedThisMonth();
    $issued = 0;
    foreach ($issuedThisMonth as $tm) {
      $issued += abs($tm->getAmount());
    }

    return $this->render("AppBundle:Dashboard:workarea.html.twig", array(
          'focus' => $focusTasks,
          'days' => $days,
          'accounts' => $accounts,
          'earned' => $earned,
          'issuedThisMonth' => $earnedLogic->getIssuedThisMonth(),
          'costOfLife' => $costOfLife,
          'issued' => $issued,
    ));
  }

}
