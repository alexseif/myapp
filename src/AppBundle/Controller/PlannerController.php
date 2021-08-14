<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Planner;
use AppBundle\Form\PlannerType;
use AppBundle\Repository\PlannerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planner")
 */
class PlannerController extends Controller
{
    /**
     * @Route("/", name="planner_index", methods={"GET"})
     */
    public function index(PlannerRepository $plannerRepository): Response
    {
        return $this->render('planner/index.html.twig', [
            'planners' => $plannerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="planner_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $planner = new Planner();
        $today = new \DateTime();
        $planner->setName($today->format('M d, Y'));
        $form = $this->createForm(PlannerType::class, $planner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planner);
            $entityManager->flush();

            return $this->redirectToRoute('planner_index');
        }

        return $this->render('planner/new.html.twig', [
            'planner' => $planner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="planner_show", methods={"GET"})
     */
    public function show(Planner $planner): Response
    {
        return $this->render('planner/show.html.twig', [
            'planner' => $planner,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="planner_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Planner $planner): Response
    {
        $form = $this->createForm(PlannerType::class, $planner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('planner_index');
        }

        return $this->render('planner/edit.html.twig', [
            'planner' => $planner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="planner_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Planner $planner): Response
    {
        if ($this->isCsrfTokenValid('delete' . $planner->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($planner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('planner_index');
    }
}
