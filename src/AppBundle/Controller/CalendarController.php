<?php

/*
 * The following content was designed & implemented under AlexSeif.com
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of CalendarController
 *
 * @author Alex Seif <me@alexseif.com>
 * 
 * @Route("/calendar")
 */
class CalendarController extends Controller
{

  /**
   * @Route("/", name="calendar_index")
   */
  public function indexAction()
  {
    /**
     * Move to utilities
      $months = array();
      for ($i = 0; $i < 12; $i++) {
      $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
      $months[date('n', $timestamp)] = date('F', $timestamp);
      }
      $months = array_reduce(range(1, 12), function($rslt, $m, $startFromThisMonth = true) {
      $rslt[$m] = date('F', mktime(0, 0, 0, $m));
      return ((count($rslt) > 11) && $startFromThisMonth) ? $rslt = array_slice($rslt, date('n') - 1, 12, true) + array_slice($rslt, 0, date('n') - 1, true) : $rslt;
      });
      $days = array_reduce(range(0, 6), function($rslt, $d) {
      $rslt[$d] = date('D', mktime(0, 0, 0, date('n'), date('j') + $d));
      return $rslt;
      });
     */
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();
    $tomorrow = new \DateTime("tomorrow");
    $calendar = [
      'calendar' => [
        'today' => [
          'todayHours' => \AppBundle\Util\WorkWeek::getDayHours($today->format('l')),
          'completed' => $em->getRepository('AppBundle:Tasks')->getCompletedToday(),
          'tasks' => $em->getRepository('AppBundle:Tasks')->focusList(20),
          'days' => $em->getRepository('AppBundle:Days')->getImportantCards(),
          'holidays' => $em->getRepository('AppBundle:Holiday')->findByRange($today, $today)
        ],
        'tomorrow' => [
          'todayHours' => \AppBundle\Util\WorkWeek::getDayHours($tomorrow->format('l')),
          'completed' => $em->getRepository('AppBundle:Tasks')->getCompletedToday(),
          'tasks' => $em->getRepository('AppBundle:Tasks')->findFocusByEta($tomorrow, 20),
          'days' => $em->getRepository('AppBundle:Days')->getImportantCards(),
          'holidays' => $em->getRepository('AppBundle:Holiday')->findByRange($tomorrow, $tomorrow)
        ]
      ]
    ];
    return $this->render("AppBundle:Calendar:index.html.twig", $calendar);
  }

}
