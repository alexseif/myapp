<?php

namespace App\Controller;

use App\Entity\CostOfLife;
use App\Form\CostOfLifeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Costoflife controller.
 *
 * @Route("costoflife")
 */
class CostOfLifeController extends AbstractController
{
    /**
     * Lists all costOfLife entities.
     *
     * @Route("/", name="costoflife_index", methods={"GET"})
     */
    public function indexAction( EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $costOfLives = $em->getRepository(CostOfLife::class)->findAll();

        return $this->render('costoflife/index.html.twig', [
            'costOfLives' => $costOfLives,
        ]);
    }

    /**
     * Creates a new costOfLife entity.
     *
     * @Route("/new", name="costoflife_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $entityManager)
    {
        $costOfLife = new Costoflife();
        $form = $this->createForm(CostOfLifeType::class, $costOfLife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($costOfLife);
            $em->flush($costOfLife);

            return $this->redirectToRoute('costoflife_show', ['id' => $costOfLife->getId()]);
        }

        return $this->render('costoflife/new.html.twig', [
            'costOfLife' => $costOfLife,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a costOfLife entity.
     *
     * @Route("/{id}", name="costoflife_show", methods={"GET"})
     */
    public function showAction(CostOfLife $costOfLife): \Symfony\Component\HttpFoundation\Response
    {
        $deleteForm = $this->createDeleteForm($costOfLife);

        return $this->render('costoflife/show.html.twig', [
            'costOfLife' => $costOfLife,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing costOfLife entity.
     *
     * @Route("/{id}/edit", name="costoflife_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, CostOfLife $costOfLife, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createDeleteForm($costOfLife);
        $editForm = $this->createForm(CostOfLifeType::class, $costOfLife);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('costoflife_edit', ['id' => $costOfLife->getId()]);
        }

        return $this->render('costoflife/edit.html.twig', [
            'costOfLife' => $costOfLife,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a costOfLife entity.
     *
     * @Route("/{id}", name="costoflife_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, CostOfLife $costOfLife, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $form = $this->createDeleteForm($costOfLife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($costOfLife);
            $em->flush($costOfLife);
        }

        return $this->redirectToRoute('costoflife_index');
    }

    /**
     * Creates a form to delete a costOfLife entity.
     *
     * @param CostOfLife $costOfLife The costOfLife entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(CostOfLife $costOfLife): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('costoflife_delete', ['id' => $costOfLife->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
