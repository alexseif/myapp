<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Scenario;
use AppBundle\Entity\ScenarioDetails;
use AppBundle\Form\ScenarioType;
use AppBundle\Repository\ScenarioDetailsRepository;
use AppBundle\Repository\ScenarioObjectiveRepository;
use AppBundle\Repository\ScenarioRepository;
use DateTime;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \DateInterval;
use \DatePeriod;

/**
 * @Route("/scenario")
 */
class ScenarioController extends Controller
{
    /**
     * @Route("/", name="scenario_index", methods={"GET"})
     */
    public function index(ScenarioRepository $scenarioRepository): Response
    {
        return $this->render('scenario/index.html.twig', [
            'scenarios' => $scenarioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="scenario_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $scenario = new Scenario();
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scenario);
            $entityManager->flush();

            return $this->redirectToRoute('scenario_index');
        }

        return $this->render('scenario/new.html.twig', [
            'scenario' => $scenario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="scenario_show", methods={"GET"})
     */
    public function show(Scenario $scenario): Response
    {
        return $this->render('scenario/show.html.twig', [
            'scenario' => $scenario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="scenario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Scenario $scenario): Response
    {
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scenario_index');
        }

        return $this->render('scenario/edit.html.twig', [
            'scenario' => $scenario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="scenario_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Scenario $scenario): Response
    {
        if ($this->isCsrfTokenValid('delete' . $scenario->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scenario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('scenario_index');
    }

    /**
     * @Route("/{id}/generate_details", name="scenario_generate_details", methods={"GET", "POST"})
     */
    public function generateDetails(Request $request, Scenario $scenario): Response
    {
        $date = new DateTime();
        $generate = new stdClass();
        $generate->title = $date->format('Y-m-d');
        $generate->startDate = $generate->endDate = $date;
        $generate->amount = 0;
        $form = $this->createFormBuilder($generate)
            ->add('title')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                )
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
                'attr' => array(
                    'class' => 'datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-MM-dd',
                )
            ])
            ->add('amount', MoneyType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $interval = new DateInterval('P1D');
            $generate->endDate->add($interval);
            $period = new DatePeriod($generate->startDate, $interval, $generate->endDate);

            // Use loop to store date into array
            foreach ($period as $date) {
                $scenarioDetails = new ScenarioDetails();
                $scenarioDetails->setDate($date);
                $scenarioDetails->setTitle($generate->title);
                $scenarioDetails->setAmount($generate->amount);
                $scenario->addScenarioDetail($scenarioDetails);
                $entityManager->persist($scenarioDetails);
            }
            $entityManager->flush();

            return $this->redirectToRoute('scenario_show', ["id" => $scenario]);
        }

        return $this->render('scenario/generate_details.html.twig', [
            'scenario' => $scenario,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/delete_details", name="scenario_delete_details", methods={"GET", "POST"})
     */
    public function deleteDetails(Request $request, Scenario $scenario)
    {
        $sdIds = $request->get('scenarioDetails');
        $sdr = $this->get(ScenarioDetailsRepository::class);
        foreach ($sdIds as $sdId) {
            $sd = $sdr->find($sdId);
            $scenario->removeScenarioDetail($sd);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('scenario_show', ["id" => $scenario]);
    }
    /**
     * @Route("/{id}/delete_objectives", name="scenario_delete_objectives", methods={"GET", "POST"})
     */
    public function deleteObjectives(Request $request, Scenario $scenario)
    {
        $sdIds = $request->get('scenarioObjectives');
        $sdr = $this->get(ScenarioObjectiveRepository::class);
        foreach ($sdIds as $sdId) {
            $sd = $sdr->find($sdId);
            $scenario->removeScenarioDetail($sd);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('scenario_show', ["id" => $scenario]);
    }
}
