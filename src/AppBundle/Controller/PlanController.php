<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plan", name="plan")
 */
class PlanController extends Controller
{
    /**
     * @Route("/", name="_index")
     */
    public function index(): Response
    {
        return $this->render('plan/index.html.twig', [
        ]);
    }



}
