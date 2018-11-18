<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

    dump($balanceEnumeration);

    return array(
      'balance' => $balanceEnumeration
    );
  }

}
