<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Util\WorkWeek;

class WorkWeekController extends Controller
{

  /**
   * @Route("/displayWorkWeek", name="workweek_show")
   */
  public function displayWorkWeekAction()
  {
    $workWeek = WorkWeek::getWorkWeek();
    return $this->render('AppBundle:WorkWeek:display_work_week.html.twig', array(
          'workWeek' => $workWeek
            // ...
    ));
  }

}
