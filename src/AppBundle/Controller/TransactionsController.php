<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Transactions;
use AppBundle\Form\TransactionsType;

/**
 * Transactions controller.
 *
 * @Route("/transactions")
 */
class TransactionsController extends Controller
{

    /**
     * Lists all Transactions entities.
     *
     * @Route("/", name="transactions_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('AppBundle:Transactions')->findAll();

        return $this->render('transactions/index.html.twig',
                array(
                'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new Transactions entity.
     *
     * @Route("/newExpense", name="new_expense")
     * @Method({"GET", "POST"})
     */
    public function newExpenseAction(Request $request)
    {
        $transaction = new Transactions();
        $form        = $this->createForm('AppBundle\Form\TransactionsType',
            $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->makeExpense();
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_show',
                    array('id' => $transaction->getId()));
        }

        return $this->render('transactions/new.html.twig',
                array(
                'transaction' => $transaction,
                'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Transactions entity.
     *
     * @Route("/newIncome", name="new_income")
     * @Method({"GET", "POST"})
     */
    public function newIncomeAction(Request $request)
    {
        $transaction = new Transactions();
        $form        = $this->createForm('AppBundle\Form\TransactionsType',
            $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_show',
                    array('id' => $transaction->getId()));
        }

        return $this->render('transactions/new.html.twig',
                array(
                'transaction' => $transaction,
                'form' => $form->createView(),
        ));
    }

    /**
     * Imports Transactions from CSV.
     *
     * @Route("/import", name="import")
     * @Method({"GET", "POST"})
     */
    public function importAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'File to Submit'))
            ->getForm();

        $form->handleRequest($request);

        $em            = $this->getDoctrine()->getManager();
        $transactions  = new \Doctrine\Common\Collections\ArrayCollection();
        $categories    = new \Doctrine\Common\Collections\ArrayCollection($em->getRepository('AppBundle:Categories')->findAll());
        $tags          = new \Doctrine\Common\Collections\ArrayCollection($em->getRepository('AppBundle:Tags')->findAll());
        $csvCategories = array();
        $csvTags       = array();

        if ($form->isSubmitted() && $form->isValid()) {
            // Get file
            $file = $form->get('submitFile');

            // Your csv file here when you hit submit button

            $rows            = array();
            $ignoreFirstLine = true;
            if (($handle          = fopen($file->getData(), "r")) !== FALSE) {

                $i = 0;

                while (($data = fgetcsv($handle)) !== FALSE) {
                    $i++;
                    if ($ignoreFirstLine && $i == 1) {
                        continue;
                    }
                    $rows[]          = $data;
                    if (!in_array($data[2], $csvCategories))
                            $csvCategories[] = $data[2];
                    $csvTags         = $data[3];
                }

                fclose($handle);

                foreach ($categories as $cat) {
                    if (in_array($cat->getCategory(), $csvCategories)) {
                        $csvCategories[$cat->getCategory()] = $cat;
                    }
                }

                foreach ($rows as $row) {
                    $transaction = new Transactions();
                    $date        = \DateTime::createFromFormat('m/d/Y', $row[0]);
                    $transaction->setDate($date);

                    if (exists($row[2], $availableCategories)) {
                        $category = $categories->get($key);
                    } else {
                        $category = new \AppBundle\Entity\Categories();
                        $category->setCategory($row[2]);
                        $categories->add($category);
                    }

                    $transaction->setCategory($category);

                    $rawTags = explode(',', $row[3]);
                    foreach ($rawTags as $rawTag) {
                        $tag = new \AppBundle\Entity\Tags();
                        $tag->setTag($rawTag);
//                        if (!$tags->contains($tag)) {
                        $tags->add($tag);
//                        }
                        $transaction->addTag($tag);
                        $transaction->setAmount($row[4] * 100);
                    }

                    $transactions->add($transaction);
                }
            }


//            $em->persist($transaction);
//            $em->flush();
//            return $this->redirectToRoute('transactions_show',
//                    array('id' => $transaction->getId()));
        }

        return $this->render('transactions/import.html.twig',
                array(
                'transactions' => $transactions,
                'categories' => $categories,
                'tags' => $tags,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Transactions entity.
     *
     * @Route("/{id}", name="transactions_show")
     * @Method("GET")
     */
    public function showAction(Transactions $transaction)
    {
        $deleteForm = $this->createDeleteForm($transaction);

        return $this->render('transactions/show.html.twig',
                array(
                'transaction' => $transaction,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Transactions entity.
     *
     * @Route("/{id}/edit", name="transactions_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Transactions $transaction)
    {
        $deleteForm = $this->createDeleteForm($transaction);
        $editForm   = $this->createForm('AppBundle\Form\TransactionsType',
            $transaction);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($transaction);
            $em->flush();

            return $this->redirectToRoute('transactions_edit',
                    array('id' => $transaction->getId()));
        }

        return $this->render('transactions/edit.html.twig',
                array(
                'transaction' => $transaction,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Transactions entity.
     *
     * @Route("/{id}", name="transactions_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Transactions $transaction)
    {
        $form = $this->createDeleteForm($transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('transactions_index');
    }

    /**
     * Creates a form to delete a Transactions entity.
     *
     * @param Transactions $transaction The Transactions entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Transactions $transaction)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('transactions_delete',
                        array('id' => $transaction->getId())))
                ->setMethod('DELETE')
                ->getForm()
        ;
    }
}