<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Weekly controller.
 *
 * @Route("/weekly")
 */
class WeeklyController extends Controller
{

  /**
   * @Route("/", name="default")
   * @Template("weekly/index.html.twig")
   */
  public function defaultAction()
  {
    $BOL = new \DateTime();
    $BOL->setDate(1982, 10, 29);
    $EOL = new \DateTime();
    $EOL->setDate(1982, 10, 29)
        ->add(new \DateInterval('P90Y'));
    $weeksLived = $BOL->diff(new \DateTime())->days / 7;
    return array(
      'BOL' => $BOL,
      'EOL' => $EOL,
      'weeksLived' => $weeksLived
    );
  }

}
