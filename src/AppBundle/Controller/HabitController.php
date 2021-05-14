<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Habit;
use AppBundle\Form\HabitType;
use AppBundle\Repository\HabitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/habit")
 */
class HabitController extends Controller
{
    /**
     * @Route("/", name="habit_index", methods={"GET"})
     */
    public function index(HabitRepository $habitRepository): Response
    {
        return $this->render('habit/index.html.twig', [
            'habits' => $habitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="habit_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $habit = new Habit();
        $form = $this->createForm(HabitType::class, $habit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($habit);
            $entityManager->flush();

            return $this->redirectToRoute('habit_index');
        }

        return $this->render('habit/new.html.twig', [
            'habit' => $habit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="habit_show", methods={"GET"})
     */
    public function show(Habit $habit): Response
    {
        return $this->render('habit/show.html.twig', [
            'habit' => $habit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="habit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Habit $habit): Response
    {
        $form = $this->createForm(HabitType::class, $habit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('habit_index');
        }

        return $this->render('habit/edit.html.twig', [
            'habit' => $habit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="habit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Habit $habit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$habit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($habit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('habit_index');
    }
}
