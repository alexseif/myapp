<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Routine;
use AppBundle\Form\RoutineType;
use AppBundle\Repository\RoutineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/routine")
 */
class RoutineController extends Controller
{
    /**
     * @Route("/", name="routine_index", methods={"GET"})
     */
    public function index(RoutineRepository $routineRepository): Response
    {
        return $this->render('routine/index.html.twig', [
            'routines' => $routineRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="routine_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $routine = new Routine();
        $form = $this->createForm(RoutineType::class, $routine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($routine);
            $entityManager->flush();

            return $this->redirectToRoute('routine_index');
        }

        return $this->render('routine/new.html.twig', [
            'routine' => $routine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="routine_show", methods={"GET"})
     */
    public function show(Routine $routine): Response
    {
        return $this->render('routine/show.html.twig', [
            'routine' => $routine,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="routine_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Routine $routine): Response
    {
        $form = $this->createForm(RoutineType::class, $routine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('routine_index');
        }

        return $this->render('routine/edit.html.twig', [
            'routine' => $routine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="routine_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Routine $routine): Response
    {
        if ($this->isCsrfTokenValid('delete'.$routine->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($routine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('routine_index');
    }
}
