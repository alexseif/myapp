<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Form\CurrencyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Currency controller.
 *
 * @Route("currency")
 */
class CurrencyController extends AbstractController
{

    /**
     * Lists all currency entities.
     *
     * @Route("/", name="currency_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $em = $entityManager;

        $currencies = $em->getRepository(Currency::class)->findAll();

        return $this->render('currency/index.html.twig', [
          'currencies' => $currencies,
        ]);
    }

    /**
     * Creates a new currency entity.
     *
     * @Route("/new", name="currency_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $currency = new Currency();
        $form = $this->createForm(CurrencyType::class, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($currency);
            $em->flush($currency);

            return $this->redirectToRoute(
              'currency_show',
              ['id' => $currency->getId()]
            );
        }

        return $this->render('currency/new.html.twig', [
          'currency' => $currency,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a currency entity.
     *
     * @Route("/{id}", name="currency_show", methods={"GET"})
     */
    public function showAction(Currency $currency): \Symfony\Component\HttpFoundation\Response
    {
        $deleteForm = $this->createDeleteForm($currency);

        return $this->render('currency/show.html.twig', [
          'currency' => $currency,
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing currency entity.
     *
     * @Route("/{id}/edit", name="currency_edit", methods={"GET", "POST"})
     */
    public function editAction(
      Request $request,
      Currency $currency,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($currency);
        $editForm = $this->createForm(CurrencyType::class, $currency);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
              'currency_edit',
              ['id' => $currency->getId()]
            );
        }

        return $this->render('currency/edit.html.twig', [
          'currency' => $currency,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a currency entity.
     *
     * @Route("/{id}", name="currency_delete", methods={"DELETE"})
     */
    public function deleteAction(
      Request $request,
      Currency $currency,
      EntityManagerInterface $entityManager
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $form = $this->createDeleteForm($currency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($currency);
            $em->flush($currency);
        }

        return $this->redirectToRoute('currency_index');
    }

    /**
     * Creates a form to delete a currency entity.
     *
     * @param Currency $currency The currency entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Currency $currency): FormInterface
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('currency_delete', ['id' => $currency->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
