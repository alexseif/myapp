<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RoutineLog;
use AppBundle\Form\RoutineLogType;
use AppBundle\Repository\RoutineLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/routine/log")
 */
class RoutineLogController extends Controller
{
    /**
     * @Route("/", name="routine_log_index", methods={"GET"})
     */
    public function index(RoutineLogRepository $routineLogRepository): Response
    {
        return $this->render('routine/logs/index.html.twig', [
            'routine_logs' => $routineLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="routine_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $routineLog = new RoutineLog();
        $form = $this->createForm(RoutineLogType::class, $routineLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($routineLog);
            $entityManager->flush();

            return $this->redirectToRoute('routine_log_index');
        }

        return $this->render('routine/logs/new.html.twig', [
            'routine_log' => $routineLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="routine_log_show", methods={"GET"})
     */
    public function show(RoutineLog $routineLog): Response
    {
        return $this->render('routine/logs/show.html.twig', [
            'routine_log' => $routineLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="routine_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RoutineLog $routineLog): Response
    {
        $form = $this->createForm(RoutineLogType::class, $routineLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('routine_log_index');
        }

        return $this->render('routine/logs/edit.html.twig', [
            'routine_log' => $routineLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="routine_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RoutineLog $routineLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$routineLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($routineLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('routine_log_index');
    }
}
