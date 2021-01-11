<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Util\WorkWeek;

/**
 * WorkDays  controller.
 *
 * @Route("/workdays")
 */
class WorkDaysController extends Controller
{

    /**
     * @Route("/", name="workdays_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:WorkDays:index.html.twig', array());
    }

    /**
     * @Route("/displayWorkWeek", name="workdays_show_week")
     */
    public function displayWorkWeekAction()
    {
        $workWeek = WorkWeek::getWorkWeek();
        return $this->render('AppBundle:WorkDays:display_work_week.html.twig', array(
            'workWeek' => $workWeek
        ));
    }

    /**
     * @Route("/month/{month}", name="workdays_show_month")
     */
    public function displayWorkMonthAction($month = null)
    {
        if (is_null($month)) {
            $tmp = new \DateTime();
            $month = $tmp->format('m');
        }

        $monthStart = \AppBundle\Util\DateRanges::getMonthStart();
        $monthEnd = \AppBundle\Util\DateRanges::getMonthEnd();

        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($monthStart, $interval, $monthEnd);
        $workingDays = [1, 2, 3, 4, 7];

        $em = $this->getDoctrine()->getManager();

        $dateTable = [];
        $monthTotal = new \stdClass();
        $monthTotal->daysTotal = 0;
        $monthTotal->hoursTotal = 0;
        foreach ($periods as $period) {
            $dateRow = new \stdClass();
            $dateRow->date = $period;
            $dateRow->workday = true;
            $dateRow->weekend = false;
            $dateRow->holiday = false;
            $dateRow->comment = null;
            if (!in_array($period->format('N'), $workingDays)) {
                $dateRow->weekend = true;
                $dateRow->workday = false;
                $dateRow->comment = 'Weekend';
            }
            $holiday = $em->getRepository('AppBundle:Holiday')->findOneBy(['date' => $period]);
            if ($holiday) {
                $dateRow->holiday = true;
                $dateRow->workday = false;
                $dateRow->comment = $holiday->getType() . " - " . $holiday->getName();
            }
            $dateTable[] = $dateRow;
            if ($dateRow->workday) {
                $monthTotal->daysTotal += 1;
                $monthTotal->hoursTotal += 4;
            }
        }
        return $this->render('AppBundle:WorkDays:display_work_month.html.twig', [
            'dateTable' => $dateTable,
            'monthTotal' => $monthTotal
        ]);
    }

}
