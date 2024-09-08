<?php

namespace App\Controller;

use App\Entity\Objective;
use App\Form\ObjectiveType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Objective controller.
 *
 * @Route("objective")
 */
class ObjectiveController extends AbstractController
{

    /**
     * Lists all objective entities.
     *
     * @Route("/", name="objective_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager
    ): Response {
        $em = $entityManager;

        $objectives = $em->getRepository(Objective::class)->findAll();

        return $this->render('objective/index.html.twig', [
          'objectives' => $objectives,
        ]);
    }

    /**
     * Creates a new objective entity.
     *
     * @Route("/new", name="objective_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $objective = new Objective();
        $form = $this->createForm(ObjectiveType::class, $objective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($objective);
            $em->flush();

            return $this->redirectToRoute(
              'objective_show',
              ['id' => $objective->getId()]
            );
        }

        return $this->render('objective/new.html.twig', [
          'objective' => $objective,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a objective entity.
     *
     * @Route("/{id}", name="objective_show", methods={"GET"})
     */
    public function showAction(Objective $objective
    ): Response {
        $deleteForm = $this->createDeleteForm($objective);

        return $this->render('objective/show.html.twig', [
          'objective' => $objective,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing objective entity.
     *
     * @Route("/{id}/edit", name="objective_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      Objective $objective,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($objective);
        $editForm = $this->createForm(ObjectiveType::class, $objective);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
              'objective_edit',
              ['id' => $objective->getId()]
            );
        }

        return $this->render('objective/edit.html.twig', [
          'objective' => $objective,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a objective entity.
     *
     * @Route("/{id}", name="objective_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      Objective $objective,
      EntityManagerInterface $entityManager
    ): RedirectResponse {
        $form = $this->createDeleteForm($objective);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($objective);
            $em->flush();
        }

        return $this->redirectToRoute('objective_index');
    }

    /**
     * Creates a form to delete a objective entity.
     *
     * @param Objective $objective The objective entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Objective $objective)
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('objective_delete', ['id' => $objective->getId()]
            )
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
