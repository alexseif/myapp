<?php

namespace App\Controller;

use App\Entity\CostOfLife;
use App\Entity\Currency;
use App\Entity\Rate;
use App\Form\RateType;
use App\Logic\CostOfLifeLogic;
use App\Service\CostService;
use App\Service\RateCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction(
      CostService $costService,
      RateCalculator $rateCalculator
    ): Response {
        $costOfLife = $costService;
        $rates = $rateCalculator->getActive();

        return $this->render('rate/index.html.twig', [
          'costOfLife' => $costOfLife,
          'rates' => $rates,
        ]);
    }

    /**
     * Creates a new rate entity.
     *
     * @Route("/new", name="rate_new", methods={"GET", "POST"})
     */
    public function newAction(
      Request $request,
      EntityManagerInterface $entityManager
    ) {
        $rate = new Rate();

        $em = $entityManager;
        $currencies = $em->getRepository(Currency::class)->findAll();
        $cost = $em->getRepository(CostOfLife::class)->sumCostOfLife()['cost'];

        $costOfLife = new CostOfLifeLogic($cost, $currencies);
        $rate->setRate($costOfLife->getHourly());

        $form = $this->createForm(RateType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rate->setActive(true);
            $em->persist($rate);
            $rates = $em->getRepository(Rate::class)->findBy(
              ['client' => $rate->getClient()]
            );
            foreach ($rates as $oldRate) {
                $oldRate->setActive(false);
            }
            $em->flush();

            return $this->redirectToRoute('rate_show', ['id' => $rate->getId()]
            );
        }

        return $this->render('rate/new.html.twig', [
          'rate' => $rate,
          'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a new rate entity.
     *
     * @Route("/increase", name="rate_increase_all", methods={"GET", "POST"})
     */
    public function increaseAllAction(
      Request $request,
      RateCalculator $rateCalculator
    ): Response {
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
                $this->addFlash(
                  'success',
                  'Rates increase by ' . $percent . '%'
                );
            } elseif (null !== $increase['fixedValue']) {
                $fixedValue = $increase['fixedValue'];
                $rateCalculator->increaseByFixedValue($fixedValue, $note);
                $this->addFlash(
                  'success',
                  'Rates increase by EGP ' . $fixedValue
                );
            } else {
                $this->addFlash('error', 'Invalide From');
            }
            $this->redirect($this->generateUrl('rate_increase_all'));
        }

        return $this->render('rate/increaseAll.html.twig', [
          'rates' => $rateCalculator->getActive(),
          'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a rate entity.
     *
     * @Route("/{id}", name="rate_show", methods={"GET"})
     */
    public function showAction(
      Rate $rate,
      EntityManagerInterface $entityManager
    ): Response {
        $deleteForm = $this->createDeleteForm($rate);
        $em = $entityManager;
        $historyRates = $em->getRepository(Rate::class)->findBy(
          ['client' => $rate->getClient()]
        );

        return $this->render('rate/show.html.twig', [
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
    public function editAction(
      Request $request,
      Rate $rate,
      EntityManagerInterface $entityManager
    ) {
        $deleteForm = $this->createDeleteForm($rate);
        $editForm = $this->createForm(RateType::class, $rate);
        $editForm->add('createdAt');
        $editForm->add('updatedAt');
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('rate_edit', ['id' => $rate->getId()]
            );
        }

        return $this->render('rate/edit.html.twig', [
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
    public function deleteAction(
      Request $request,
      Rate $rate,
      EntityManagerInterface $entityManager
    ): RedirectResponse {
        $form = $this->createDeleteForm($rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
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
    private function createDeleteForm(Rate $rate
    ): FormInterface {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('rate_delete', ['id' => $rate->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
