<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PriceRate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pricerate controller.
 *
 * @Route("pricerate")
 */
class PriceRateController extends Controller
{

  /**
   * Lists all priceRate entities.
   *
   * @Route("/", name="pricerate_index", methods={"GET"})
   * @Template("AppBundle:pricerate:index.html.twig")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $priceRates = $em->getRepository('AppBundle:PriceRate')->findAll();

    return array(
      'priceRates' => $priceRates,
    );
  }

  /**
   * Creates a new priceRate entity.
   *
   * @Route("/new", name="pricerate_new", methods={"GET", "POST"})
   * @Template("AppBundle:pricerate:new.html.twig")
   */
  public function newAction(Request $request)
  {
    $priceRate = new Pricerate();
    $form = $this->createForm('AppBundle\Form\PriceRateType', $priceRate);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($priceRate);
      $em->flush();

      return $this->redirectToRoute('pricerate_show', array('id' => $priceRate->getId()));
    }

    return array(
      'priceRate' => $priceRate,
      'form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a priceRate entity.
   *
   * @Route("/{id}", name="pricerate_show", methods={"GET"})
   * @Template("AppBundle:pricerate:show.html.twig")
   */
  public function showAction(PriceRate $priceRate)
  {
    $deleteForm = $this->createDeleteForm($priceRate);

    return array(
      'priceRate' => $priceRate,
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing priceRate entity.
   *
   * @Route("/{id}/edit", name="pricerate_edit", methods={"GET", "POST"})
   * @Template("AppBundle:pricerate:edit.html.twig")
   */
  public function editAction(Request $request, PriceRate $priceRate)
  {
    $deleteForm = $this->createDeleteForm($priceRate);
    $editForm = $this->createForm('AppBundle\Form\PriceRateType', $priceRate);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('pricerate_edit', array('id' => $priceRate->getId()));
    }

    return array(
      'priceRate' => $priceRate,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Deletes a priceRate entity.
   *
   * @Route("/{id}", name="pricerate_delete", methods={"DELETE"})
   */
  public function deleteAction(Request $request, PriceRate $priceRate)
  {
    $form = $this->createDeleteForm($priceRate);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($priceRate);
      $em->flush();
    }

    return $this->redirectToRoute('pricerate_index');
  }

  /**
   * Creates a form to delete a priceRate entity.
   *
   * @param PriceRate $priceRate The priceRate entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(PriceRate $priceRate)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('pricerate_delete', array('id' => $priceRate->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
