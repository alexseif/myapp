<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Scenario;
use AppBundle\Entity\ScenarioObjective;
use AppBundle\Form\ScenarioObjectiveType;
use AppBundle\Repository\ScenarioObjectiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scenario/objective")
 */
class ScenarioObjectiveController extends Controller
{
    /**
     * @Route("/{scenario}", name="scenario_objective_index", methods={"GET"})
     */
    public function index(Scenario $scenario, ScenarioObjectiveRepository $scenarioObjectiveRepository): Response
    {
        return $this->render('scenario_objective/index.html.twig', [
            'scenario' => $scenario,
            'scenario_objectives' => $scenarioObjectiveRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{scenario}/new", name="scenario_objective_new", methods={"GET","POST"})
     */
    public function new(Request $request, Scenario $scenario): Response
    {
        $scenarioObjective = new ScenarioObjective();
        $form = $this->createForm(ScenarioObjectiveType::class, $scenarioObjective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scenarioObjective);
            $entityManager->flush();

            return $this->redirectToRoute('scenario_show', ['id' => $scenario]);
        }

        return $this->render('scenario_objective/new.html.twig', [
            'scenario' => $scenario,
            'scenario_objective' => $scenarioObjective,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{scenario}/{id}", name="scenario_objective_show", methods={"GET"})
     */
    public function show(Scenario $scenario, ScenarioObjective $scenarioObjective): Response
    {
        return $this->render('scenario_objective/show.html.twig', [
            'scenario' => $scenario,
            'scenario_objective' => $scenarioObjective,
        ]);
    }

    /**
     * @Route("/{scenario}/{id}/edit", name="scenario_objective_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Scenario $scenario, ScenarioObjective $scenarioObjective): Response
    {
        $form = $this->createForm(ScenarioObjectiveType::class, $scenarioObjective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scenario_show', ['id' => $scenario]);
        }

        return $this->render('scenario_objective/edit.html.twig', [
            'scenario' => $scenario,
            'scenario_objective' => $scenarioObjective,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{scenario}/{id}", name="scenario_objective_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Scenario $scenario, ScenarioObjective $scenarioObjective): Response
    {
        if ($this->isCsrfTokenValid('delete' . $scenarioObjective->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scenarioObjective);
            $entityManager->flush();
        }

        return $this->redirectToRoute('scenario_objective_index', ["scenario" => $scenario]);
    }
}
