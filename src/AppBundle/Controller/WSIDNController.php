<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\WSIDN;
use AppBundle\Entity\Slot;

/**
 * Days controller.
 *
 * @Route("/wsidn")
 */
class WSIDNController extends Controller
{

    /**
     * Lists all Days entities.
     *
     * @Route("/", name="wsidn_index")
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $days = $em->getRepository('AppBundle:Days')->getActiveCards();
        $wsidn = new WSIDN();
        $meals = array(
            "Breakfast",
            "Snack",
            "Lunch",
            "Snack",
            "Dinner"
        );
        $chores = array();
        $mealIndex = 0;
        $workTotal = 0;
        do {
            if (floor($wsidn->getTotal() / 180) == $mealIndex && $mealIndex < count($meals)) {
                $wsidn->addSlot(new Slot($meals[$mealIndex], 20));
                $stretchSlot = new Slot("Stretch", 5);
                $wsidn->addSlot($stretchSlot);
                $mealIndex++;
                continue;
            }
            if (!$wsidn->isLastSlot("Work")) {
                $workTotal += 45;
                $workSlot = new Slot("Work", 45);
                $wsidn->addSlot($workSlot);
                continue;
            }
            if (!$wsidn->isLastSlot("Stretch")) {
                $stretchSlot = new Slot("Stretch", 5);
                $wsidn->addSlot($stretchSlot);
            }
        } while ($wsidn->isSlotsAvailable() && $workTotal <= 480);
        return $this->render('wsidn/index.html.twig', array(
                    'wsidn' => $wsidn,
        ));
    }

}
