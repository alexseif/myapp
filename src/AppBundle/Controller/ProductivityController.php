<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @Route("productivity")
 */
class ProductivityController extends Controller
{

  /**
   *
   * @Route("/", name="productivity_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    return $this->render('AppBundle:productivity:index.html.twig', array(
    ));
  }

}
