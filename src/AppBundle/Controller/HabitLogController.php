<?php

namespace AppBundle\Controller;

use AppBundle\Entity\HabitLog;
use AppBundle\Form\HabitLogType;
use AppBundle\Repository\HabitLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/habit/log")
 */
class HabitLogController extends Controller
{
    /**
     * @Route("/", name="habit_log_index", methods={"GET"})
     */
    public function index(HabitLogRepository $habitLogRepository): Response
    {
        return $this->render('habit_log/index.html.twig', [
            'habit_logs' => $habitLogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="habit_log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $habitLog = new HabitLog();
        $form = $this->createForm(HabitLogType::class, $habitLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($habitLog);
            $entityManager->flush();

            return $this->redirectToRoute('habit_log_index');
        }

        return $this->render('habit_log/new.html.twig', [
            'habit_log' => $habitLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="habit_log_show", methods={"GET"})
     */
    public function show(HabitLog $habitLog): Response
    {
        return $this->render('habit_log/show.html.twig', [
            'habit_log' => $habitLog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="habit_log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, HabitLog $habitLog): Response
    {
        $form = $this->createForm(HabitLogType::class, $habitLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('habit_log_index');
        }

        return $this->render('habit_log/edit.html.twig', [
            'habit_log' => $habitLog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="habit_log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, HabitLog $habitLog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$habitLog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($habitLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('habit_log_index');
    }
}
