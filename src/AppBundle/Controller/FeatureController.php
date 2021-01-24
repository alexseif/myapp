<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feature;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\FeatureType;
use AppBundle\Repository\FeatureRepository;
use AppBundle\Repository\TaskListsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/feature")
 */
class FeatureController extends Controller
{
    /**
     * @Route("/", name="feature_index", methods={"GET"})
     */
    public function index(FeatureRepository $featureRepository, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('feature_index'))
            ->setMethod('GET')
            ->add('list', EntityType::class, [
                'class' => TaskLists::class,
                'query_builder' => /**
                 * @param TaskListsRepository $er
                 * @return int|mixed|string
                 */ function (TaskListsRepository $er) {
                    return $er->findActiveQuery();
                },
                'group_by' => function ($taskList) {
                    if ($taskList->getAccount()) {
                        if ($taskList->getAccount()->getClient()) {
                            return $taskList->getAccount()->getClient()->getName();
                        }
                        return $taskList->getAccount()->getName();
                    }
                    return "N/A";
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'chosen',
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $features = $featureRepository->findBy(['list' => $form->getData()['list']]);
        } else {
            $features = $featureRepository->findAll();
        }

        return $this->render('feature/index.html.twig', [
            'features' => $features,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="feature_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $feature = new Feature();
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('feature_index');
        }

        return $this->render('feature/new.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feature_show", methods={"GET"})
     */
    public function show(Feature $feature): Response
    {
        return $this->render('feature/show.html.twig', [
            'feature' => $feature,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="feature_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Feature $feature): Response
    {
        $form = $this->createForm(FeatureType::class, $feature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('feature_index');
        }

        return $this->render('feature/edit.html.twig', [
            'feature' => $feature,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feature_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Feature $feature): Response
    {
        if ($this->isCsrfTokenValid('delete' . $feature->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($feature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('feature_index');
    }
}
