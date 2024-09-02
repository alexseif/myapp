<?php

namespace App\Controller;

use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Weekly controller.
 *
 * @Route("/weekly")
 */
class WeeklyController extends AbstractController
{

    /**
     * @Route("/", name="default")
     */
    public function defaultAction(): Response
    {
        $BOL = new DateTime();
        $BOL->setDate(1982, 10, 29);
        $EOL = new DateTime();
        $EOL->setDate(1982, 10, 29)
          ->add(new DateInterval('P90Y'));
        $weeksLived = $BOL->diff(new DateTime())->days / 7;

        return $this->render('weekly/index.html.twig', [
          'BOL' => $BOL,
          'EOL' => $EOL,
          'weeksLived' => $weeksLived,
        ]);
    }

}
