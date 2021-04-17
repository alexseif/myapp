<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Scenario;
use AppBundle\Entity\ScenarioDetails;
use AppBundle\Form\ScenarioDetailsType;
use AppBundle\Repository\ScenarioDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scenario/details")
 */
class ScenarioDetailsController extends Controller
{
    /**
     * @Route("/{scenario}", name="scenario_details_index", methods={"GET"})
     */
    public function index(Scenario $scenario, ScenarioDetailsRepository $scenarioDetailsRepository): Response
    {
        return $this->render('scenario_details/index.html.twig', [
            'scenario' => $scenario,
            'scenario_details' => $scenarioDetailsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{scenario}/new", name="scenario_details_new", methods={"GET","POST"})
     */
    public function new(Request $request, Scenario $scenario): Response
    {
        $scenarioDetail = new ScenarioDetails();
        $scenarioDetail->setScenario($scenario);
        $form = $this->createForm(ScenarioDetailsType::class, $scenarioDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scenarioDetail);
            $entityManager->flush();

            return $this->redirectToRoute('scenario_show', ["id" => $scenario]);
        }

        return $this->render('scenario_details/new.html.twig', [
            'scenario' => $scenario,
            'scenario_detail' => $scenarioDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{scenario}/{id}", name="scenario_details_show", methods={"GET"})
     */
    public function show(ScenarioDetails $scenarioDetail, Scenario $scenario): Response
    {
        return $this->render('scenario_details/show.html.twig', [
            'scenario' => $scenario,
            'scenario_detail' => $scenarioDetail,
        ]);
    }

    /**
     * @Route("/{scenario}/{id}/edit", name="scenario_details_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Scenario $scenario, ScenarioDetails $scenarioDetail): Response
    {
        $form = $this->createForm(ScenarioDetailsType::class, $scenarioDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scenario_show', ['id' => $scenario]);
        }

        return $this->render('scenario_details/edit.html.twig', [
            'scenario' => $scenario,
            'scenario_detail' => $scenarioDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{scenario}/{id}", name="scenario_details_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Scenario $scenario, ScenarioDetails $scenarioDetail): Response
    {
        if ($this->isCsrfTokenValid('delete' . $scenarioDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scenarioDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('scenario_show', ["scenario" => $scenario]);
    }
}
