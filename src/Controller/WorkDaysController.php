<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Util\WorkWeek;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * WorkDays  controller.
 *
 * @Route("/workdays")
 */
class WorkDaysController extends AbstractController
{

    /**
     * @Route("/", name="workdays_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:WorkDays:index.html.twig', []);
    }

    /**
     * @Route("/displayWorkWeek", name="workdays_show_week")
     */
    public function displayWorkWeekAction()
    {
        $workWeek = WorkWeek::getWorkWeek();

        return $this->render('AppBundle:WorkDays:display_work_week.html.twig', [
          'workWeek' => $workWeek,
        ]);
    }

    /**
     * @Route("/month/{month}", name="workdays_show_month")
     */
    public function displayWorkMonthAction(
      EntityManagerInterface $entityManager,
      $month = null
    ) {
        if (is_null($month)) {
            $tmp = new \DateTime();
            $month = $tmp->format('m');
        }

        $monthStart = \Util\DateRanges::getMonthStart();
        $monthEnd = \Util\DateRanges::getMonthEnd();

        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($monthStart, $interval, $monthEnd);
        $workingDays = [1, 2, 3, 4, 7];

        $em = $entityManager;

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
            $holiday = $em->getRepository(Holiday::class)->findOneBy(
              ['date' => $period]
            );
            if ($holiday) {
                $dateRow->holiday = true;
                $dateRow->workday = false;
                $dateRow->comment = $holiday->getType(
                  ) . ' - ' . $holiday->getName();
            }
            $dateTable[] = $dateRow;
            if ($dateRow->workday) {
                ++$monthTotal->daysTotal;
                $monthTotal->hoursTotal += 4;
            }
        }

        return $this->render(
          'AppBundle:WorkDays:display_work_month.html.twig',
          [
            'dateTable' => $dateTable,
            'monthTotal' => $monthTotal,
          ]
        );
    }

}
