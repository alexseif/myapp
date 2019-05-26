<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rate controller.
 *
 * @Route("rate")
 */
class RateController extends Controller
{

  /**
   * Lists all rate entities.
   *
   * @Route("/", name="rate_index")
   * @Method("GET")
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $rates = $em->getRepository('AppBundle:Rate')->findAll();

    return $this->render('AppBundle:rate:index.html.twig', array(
          'rates' => $rates,
    ));
  }

  /**
   * Creates a new rate entity.
   *
   * @Route("/new", name="rate_new")
   * @Method({"GET", "POST"})
   */
  public function newAction(Request $request)
  {
    $rate = new Rate();

    $em = $this->getDoctrine()->getManager();
    $currencies = $em->getRepository('AppBundle:Currency')->findAll();
    $cost = $em->getRepository('AppBundle:CostOfLife')->sumCostOfLife()["cost"];

    $costOfLife = new \AppBundle\Logic\CostOfLifeLogic($cost, $currencies);
    $rate->setRate($costOfLife->getHourly());


    $form = $this->createForm('AppBundle\Form\RateType', $rate);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $rate->setActive(true);
      $em->persist($rate);
      $rates = $em->getRepository("AppBundle:Rate")->findBy(array("client" => $rate->getClient()));
      foreach ($rates as $oldRate) {
        $oldRate->setActive(false);
      }
      $em->flush();

      return $this->redirectToRoute('rate_show', array('id' => $rate->getId()));
    }

    return $this->render('AppBundle:rate:new.html.twig', array(
          'rate' => $rate,
          'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a rate entity.
   *
   * @Route("/{id}", name="rate_show")
   * @Method("GET")
   */
  public function showAction(Rate $rate)
  {
    $deleteForm = $this->createDeleteForm($rate);

    return $this->render('AppBundle:rate:show.html.twig', array(
          'rate' => $rate,
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing rate entity.
   *
   * @Route("/{id}/edit", name="rate_edit")
   * @Method({"GET", "POST"})
   */
  public function editAction(Request $request, Rate $rate)
  {
    $deleteForm = $this->createDeleteForm($rate);
    $editForm = $this->createForm('AppBundle\Form\RateType', $rate);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->getDoctrine()->getManager()->flush();

      return $this->redirectToRoute('rate_edit', array('id' => $rate->getId()));
    }

    return $this->render('AppBundle:rate:edit.html.twig', array(
          'rate' => $rate,
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a rate entity.
   *
   * @Route("/{id}", name="rate_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, Rate $rate)
  {
    $form = $this->createDeleteForm($rate);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($rate);
      $em->flush();
    }

    return $this->redirectToRoute('rate_index');
  }

  /**
   * Creates a form to delete a rate entity.
   *
   * @param Rate $rate The rate entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Rate $rate)
  {
    return $this->createFormBuilder()
            ->setAction($this->generateUrl('rate_delete', array('id' => $rate->getId())))
            ->setMethod('DELETE')
            ->getForm()
    ;
  }

}
