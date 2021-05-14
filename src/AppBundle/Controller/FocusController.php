<?php

namespace AppBundle\Controller;

use AppBundle\Service\FocusService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\TaskLists;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FocusController extends Controller
{

    /**
     *
     * @Route("/focus", name="focus")
     */
    public function focusAction(Request $request, FocusService $focusService): ?Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('client')) {
            $client = $em->getRepository('AppBundle:Client')->find($request->get('client'));
            if (empty($client)) {
                throw new NotFoundHttpException("Client not found");
            }
            $focusService->setTasks($client);
        } else {
            $focusService->setTasks();
        }

        return $this->render("focus/index.html.twig", $focusService->get());
    }

    /**
     *
     * @Route("/focus/{name}", name="focus_tasklist")
     * @param TaskLists $tasklist
     * @param FocusService $focusService
     * @return Response|null
     */
    public function focusByTaskListAction(TaskLists $tasklist, FocusService $focusService): ?Response
    {
        $focusService->setTasksByTaskList($tasklist);

        return $this->render("focus/index.html.twig", $focusService->get());
    }
}