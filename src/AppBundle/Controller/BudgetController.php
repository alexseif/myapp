<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Logic\BalanceEnumeration;

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

    $em = $this->getDoctrine()->getManager();

    $balance = $em->getRepository('AppBundle:Balance')->findLast();

    $balanceEnumeration = new BalanceEnumeration($balance);

    return array(
      'balance' => $balanceEnumeration
    );
  }

}
