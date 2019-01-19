<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Currency controller.
 *
 * @Route("currency")
 */
class CurrencyController extends Controller
{

  /**
   * Lists all currency entities.
   *
   * @Route("/", name="currency_index", methods={"GET"})
   * @Template("AppBundle:currency:index.html.twig")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $currencies = $em->getRepository('AppBundle:Currency')->findAll();

    return array(
      'currencies' => $currencies,
    );
  }

  /**
   * Creates a new currency entity.
   *
   * @Route("/new", name="currency_new", methods={"GET", "POST"})
   * @Template("AppBundle:currency:new.html.twig")
   */
  public function newAction(Request $request)
  {
    $currency = new Currency();
    $form = $this->createForm('AppBundle\Form\CurrencyType', $currency);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($currency);
      $em->flush($currency);

      return $this->redirectToRoute('currency_show', array('id' => $currency->getId()));
    }

    return array(
      'currency' => $currency,
      'form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a currency entity.
   *
   * @Route("/{id}", name="currency_show", methods={"GET"})
   * @Template("AppBundle:currency:show.html.twig")
   */
  public function showAction(Currency $currency)
  {
    $deleteForm = $this->createDeleteForm($currency);

    return array(
      'currency' => $currency,
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing currency entity.
   *
   * @Route("/{id}/edit", name="currency_edit", methods={"GET", "POST"})
   * @Template("AppBundle:currency:edit.html.twig")
   */
  public function editAction(Request $request, Currency $currency)
  {
    $deleteForm = $this->createDeleteForm($currency);
    $editForm = $this->createForm('AppBundle\Form\CurrencyType', $currency);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('currency_edit', array('id' => $currency->getId()));
    }

    return array(
      'currency' => $currency,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Deletes a currency entity.
   *
   * @Route("/{id}", name="currency_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, Currency $currency)
  {
    $form = $this->createDeleteForm($currency);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
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
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Currency $currency)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('currency_delete', array('id' => $currency->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
