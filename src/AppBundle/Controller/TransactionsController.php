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

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            // Get file
            $file = $form->get('submitFile');

            // Your csv file here when you hit submit button

            $rows            = array();
            $ignoreFirstLine = false;
            if (($handle          = fopen($file->getData(), "r")) !== FALSE) {

                $i = 0;

                while (($data = fgetcsv($handle)) !== FALSE) {
                    $i++;
                    if ($ignoreFirstLine && $i == 1) {
                        continue;
                    }
                    $rows[] = $data;
                }

                fclose($handle);

                foreach ($rows as $row) {
                    $row[2] = ucfirst(trim($row[2]));
                    if ("" == $row[2]) {
                        continue;
                    }
                    $cat = $em->getRepository('AppBundle:Categories')->findOneBy(array(
                        'category' => $row[2]));
                    if (!$cat) {
                        $category = new \AppBundle\Entity\Categories();
                        $category->setCategory($row[2]);
                        $em->persist($category);
                        $em->flush();
                        $em->clear();
                    }
                }

                foreach ($rows as $row) {
                    $rawTags = explode(',', $row[3]);
                    foreach ($rawTags as $rawTag) {
                        $rawTag = ucfirst(trim($rawTag));
                        if ("" == $rawTag) {
                            continue;
                        }
                        $tg = $em->getRepository('AppBundle:Tags')->findOneBy(array(
                            'tag' => $rawTag));
                        if (!$tg) {
                            $tag = new \AppBundle\Entity\Tags();
                            $tag->setTag($rawTag);
                            $em->persist($tag);
                            $em->flush();
                            $em->clear();
                        }
                    }
                }
                $transactions = new \Doctrine\Common\Collections\ArrayCollection();
                foreach ($rows as $row) {
                    $transaction = new Transactions();
                    $date        = \DateTime::createFromFormat('m/d/y', $row[0]);
                    $transaction->setDate($date);
                    $row[2]      = ucfirst($row[2]);
                    if ("" != $row[2]) {
                        $cat = $em->getRepository('AppBundle:Categories')->findOneBy(array(
                            'category' => $row[2]));
                        if ($cat) {
                            $transaction->setCategory($cat);
                        }
                    }
                    $rawTags = explode(',', $row[3]);
                    foreach ($rawTags as $rawTag) {
                        $rawTag = ucfirst(trim($rawTag));
                        if ("" != $rawTag) {
                            $tg = $em->getRepository('AppBundle:Tags')->findOneBy(array(
                                'tag' => $rawTag));
                            if ($tg) {
                                $transaction->addTag($tg);
                            }
                        }
                    }
                    
                    $row[4] = (float) abs(str_replace(',', '', $row[4]));
                    $row[5] = (float) abs(str_replace(',', '', $row[5]));
                    if ($row[4] > 0) {
                        $transaction->setAmount($row[4] * 100);
                        $transaction->makeExpense();
                    } elseif ($row[5] > 0) {
                        $transaction->setAmount($row[5] * 100);
                    } else {
                        dump($row);
                    }
                    $transactions->add($transaction);
                }
                dump(count($rows));
                dump($transactions->count());
            }
            
            foreach ($transactions as $txn) {
                if($txn->getTags()->count()<1){
                    dump($txn);
                }
                if(!$txn->getCategory()->getId()){
                    dump($txn);
                }
                $em->persist($txn);
                $em->flush();
                $em->clear();
            }
//            return $this->redirectToRoute('transactions_show',
//                    array('id' => $transaction->getId()));
        }

        return $this->render('transactions/import.html.twig',
                array(
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