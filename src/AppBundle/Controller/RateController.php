<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CostOfLife;
use AppBundle\Entity\Currency;
use AppBundle\Entity\Rate;
use AppBundle\Form\RateType;
use AppBundle\Logic\CostOfLifeLogic;
use AppBundle\Service\CostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Rate controller.
 *
 * @Route("rate")
 */
class RateController extends AbstractController
{
    /**
     * Lists all rate entities.
     *
     * @Route("/", name="rate_index", methods={"GET"})
     */
    public function indexAction(CostService  $costService)
    {
        $costOfLife = $costService;
        $rateCalculator = $this->get('myapp.rate.calculator');
        $rates = $rateCalculator->getActive();

        return $this->render('@App/rate/index.html.twig', [
            'costOfLife' => $costOfLife,
            'rates' => $rates,
        ]);
    }

    /**
     * Creates a new rate entity.
     *
     * @Route("/new", name="rate_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $rate = new Rate();

        $em = $this->getDoctrine()->getManager();
        $currencies = $em->getRepository(Currency::class)->findAll();
        $cost = $em->getRepository(CostOfLife::class)->sumCostOfLife()['cost'];

        $costOfLife = new CostOfLifeLogic($cost, $currencies);
        $rate->setRate($costOfLife->getHourly());

        $form = $this->createForm(RateType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rate->setActive(true);
            $em->persist($rate);
            $rates = $em->getRepository(Rate::class)->findBy(['client' => $rate->getClient()]);
            foreach ($rates as $oldRate) {
                $oldRate->setActive(false);
            }
            $em->flush();

            return $this->redirectToRoute('rate_show', ['id' => $rate->getId()]);
        }

        return $this->render('@App/rate/new.html.twig', [
            'rate' => $rate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new rate entity.
     *
     * @Route("/increase", name="rate_increase_all", methods={"GET", "POST"})
     */
    public function increaseAllAction(Request $request)
    {
        $rateCalculator = $this->get('myapp.rate.calculator');
        $form = $this->createFormBuilder()
            ->add('percent', PercentType::class)
            ->add('fixedValue', MoneyType::class)
            ->add('note')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $increase = $form->getData();
            $note = $increase['note'];
            if (null !== $increase['percent']) {
                $percent = 1 + $increase['percent'];
                $rateCalculator->increaseByPercent($percent);
                $this->addFlash('success', 'Rates increase by '.$percent.'%');
            } elseif (null !== $increase['fixedValue']) {
                $fixedValue = $increase['fixedValue'];
                $rateCalculator->increaseByFixedValue($fixedValue, $note);
                $this->addFlash('success', 'Rates increase by EGP '.$fixedValue);
            } else {
                $this->addFlash('error', 'Invalide From');
            }
            $this->redirect($this->generateUrl('rate_increase_all'));
        }

        return $this->render('@App/rate/increaseAll.html.twig', [
            'rates' => $rateCalculator->getActive(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a rate entity.
     *
     * @Route("/{id}", name="rate_show", methods={"GET"})
     */
    public function showAction(Rate $rate)
    {
        $deleteForm = $this->createDeleteForm($rate);
        $em = $this->getDoctrine()->getManager();
        $historyRates = $em->getRepository(Rate::class)->findBy(['client' => $rate->getClient()]);

        return $this->render('@App/rate/show.html.twig', [
            'rate' => $rate,
            'historyRates' => $historyRates,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing rate entity.
     *
     * @Route("/{id}/edit", name="rate_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Rate $rate)
    {
        $deleteForm = $this->createDeleteForm($rate);
        $editForm = $this->createForm(RateType::class, $rate);
        $editForm->add('createdAt');
        $editForm->add('updatedAt');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('rate_edit', ['id' => $rate->getId()]);
        }

        return $this->render('@App/rate/edit.html.twig', [
            'rate' => $rate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a rate entity.
     *
     * @Route("/{id}", name="rate_delete", methods={"DELETE"})
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
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Rate $rate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rate_delete', ['id' => $rate->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
