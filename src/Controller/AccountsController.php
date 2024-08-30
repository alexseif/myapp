<?php

namespace App\Controller;

use App\Entity\Accounts;
use App\Entity\Tasks;
use App\Form\AccountsType;
use App\Util\DateRanges;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Accounts controller.
 *
 * @Route("/accounts")
 */
class AccountsController extends AbstractController
{

    /**
     * Lists all Accounts entities.
     *
     * @Route("/", name="accounts_index", methods={"GET"})
     */
    public function indexAction(EntityManagerInterface $entityManager)
    {
        $em = $entityManager;

        $accounts = $em->getRepository(Accounts::class)->findWithBalance();
        return $this->render('accounts/index.html.twig', [
          'accounts' => $accounts,
        ]);
    }

    /**
     * Creates a new Accounts entity.
     *
     * @Route("/new", name="accounts_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $entityManager)
    {
        $account = new Accounts();
        $form = $this->createForm(AccountsType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute(
              'accounts_show',
              ['id' => $account->getId()]
            );
        }

        return $this->render('accounts/new.html.twig', [
          'account' => $account,
          'account_form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Accounts entity.
     *
     * @Route("/{id}", name="accounts_show", methods={"GET"})
     */
    public function showAction(Accounts $account)
    {
        $deleteForm = $this->createDeleteForm($account);

        return $this->render('accounts/show.html.twig', [
          'account' => $account,
          'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * @Route("/{id}/timesheets", name="account_timesheets")
     *
     * @throws Exception
     */
    public function timesheetsAction(Accounts $account): ?Response
    {
        $today = new DateTime();
        $monthsArray = DateRanges::populateMonths($account->getCreatedAt()->format('Ymd'), $today->format('Ymd'),
          25);

        return $this->render('accounts/report.html.twig', [
          'account' => $account,
          'monthsArray' => $monthsArray,
        ]);
    }

    /**
     * @Route("/{id}/{from}/{to}", name="account_timesheet")
     *
     * @throws Exception
     */
    public function timesheetAction(Accounts $account, $from, $to, EntityManagerInterface $entityManager): ?Response
    {
        $em = $entityManager;
        $fromDate = new DateTime($from);
        $fromDate->setTime(0, 0, 0);
        $toDate = new DateTime($to);
        $toDate->setTime(23, 23, 59);
        $tasks = $em->getRepository(Tasks::class)->findCompletedByClientByRange(
          $account->getClient(),
          $fromDate,
          $toDate
        );
        $workingDays = 22;
        $total = 0;


        foreach ($tasks as $task) {
            $total += $task->getDuration();
        }
        $totalHours = $total / 60;
        $totalMins = $total % 60;

        return $this->render('accounts/timesheet.html.twig', [
          'account' => $account,
          'from' => $from,
          'to' => $to,
          'tasks' => $tasks,
          'total' => $total,
        ]);
    }

    /**
     * Displays a form to edit an existing Accounts entity.
     *
     * @Route("/{id}/edit", name="accounts_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Accounts $account, EntityManagerInterface $entityManager)
    {
        $deleteForm = $this->createDeleteForm($account);
        $editForm = $this->createForm(AccountsType::class, $account);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $entityManager;
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute(
              'accounts_edit',
              ['id' => $account->getId()]
            );
        }

        return $this->render('accounts/edit.html.twig', [
          'account' => $account,
          'account_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a Accounts entity.
     *
     * @Route("/{id}", name="accounts_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Accounts $account, EntityManagerInterface $entityManager)
    {
        $form = $this->createDeleteForm($account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $entityManager;
            $em->remove($account);
            $em->flush();
        }

        return $this->redirectToRoute('accounts_index');
    }

    /**
     * Creates a form to delete a Accounts entity.
     *
     * @param Accounts $account The Accounts entity
     *
     * @return \Symfony\Component\Form\FormInterface The form
     */
    private function createDeleteForm(Accounts $account)
    {
        return $this->createFormBuilder()
          ->setAction(
            $this->generateUrl('accounts_delete', ['id' => $account->getId()])
          )
          ->setMethod('DELETE')
          ->getForm();
    }

}
