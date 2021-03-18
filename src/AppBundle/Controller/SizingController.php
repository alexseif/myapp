<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TaskLists;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sizing", name="sizing")
 */
class SizingController extends Controller
{
    /**
     * @Route("/", name="_index")
     * @return Response
     */
    public function index(): Response
    {
        $tasklists = $this->getDoctrine()->getRepository(TaskLists::class)->findActive();
        return $this->render('sizing/index.html.twig', [
            'tasklists' => $tasklists
        ]);
    }

    /**
     * @param TaskLists $taskList
     * @Route ("/{id}/", name="_tasklist")
     */
    public function sizingTasklist(TaskLists $tasklist)
    {
        return $this->render('sizing/tasklist.html.twig', [
            'tasklist' => $tasklist
        ]);
    }
}
