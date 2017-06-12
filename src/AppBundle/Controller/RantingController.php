<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Ranting controller.
 *
 * @Route("/ranting")
 */
class RantingController extends Controller
{

  /**
   * @Route("/")
   * @Template()
   */
  public function indexAction()
  {
    
  }

}
