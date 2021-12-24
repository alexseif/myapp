<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Weekly controller.
 *
 * @Route("/weekly")
 */
class WeeklyController extends Controller
{
    /**
     * @Route("/", name="default")
     */
    public function defaultAction()
    {
        $BOL = new \DateTime();
        $BOL->setDate(1982, 10, 29);
        $EOL = new \DateTime();
        $EOL->setDate(1982, 10, 29)
            ->add(new \DateInterval('P90Y'));
        $weeksLived = $BOL->diff(new \DateTime())->days / 7;

        return $this->render('AppBundle:weekly:index.html.twig', [
            'BOL' => $BOL,
            'EOL' => $EOL,
            'weeksLived' => $weeksLived,
        ]);
    }
}
