<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Experiments controller.
 *
 * @Route("/experiments")
 */
class ExperimentsController extends Controller
{

  /**
   * @Route("/index", name="experiments_index")
   */
  public function indexAction()
  {
    $experiments = [
      "Tasks" => "experiment_tasks",
      "Accounts" => "experiment_accounts"
    ];
    return $this->render('AppBundle:Experiments:index.html.twig', array(
          "experiments" => $experiments,
    ));
  }

  /**
   * @Route("/tasks", name="experiment_tasks", methods={"GET", "POST"})
   */
  public function listTasksAction(Request $request)
  {
    $data = [
      "experiments" => ["Rate Task" => "experiment_tasks"],
      "tasks" => $this->getDoctrine()->getRepository('AppBundle:Tasks')->findAll()
    ];
    $form = $this->createFormBuilder($data)
        ->add("experiments", \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
          'mapped' => FALSE,
          'choices' => $data['experiments'],
          'placeholder' => 'Select an experiment',
          'attr' => array(
            'class' => 'chosen',
          )
        ))
        ->add("tasks", \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
          'class' => 'AppBundle:Tasks',
          'placeholder' => 'Select a task',
          'attr' => array(
            'class' => 'chosen',
          )
        ))
        ->add('Yalla', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
          'attr' => array(
            'class' => 'btn btn-success float-right',
          )
        ))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $formData = $form->getData();
      $rateCaltulcator = $this->get('myapp.rate.calculator');

      $data['result']['experiment'] = $formData['experiments'];
      $data['result']['task'] = $formData['tasks'];

      $data['result']['price'] = $rateCaltulcator->task($formData['tasks']);
    }

    return $this->render('AppBundle:Experiments:tasks.html.twig', array(
          "form" => $form->createView(),
          "data" => $data,
    ));
  }

  /**
   * @Route("/accounts", name="experiment_accounts", methods={"GET", "POST"})
   */
  public function accountsAction(Request $request)
  {
    $data = [
      "experiments" => ["Account" => "experiment_accounts"],
    ];
    $form = $this->createFormBuilder($data)
        ->add("experiments", \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
          'mapped' => FALSE,
          'choices' => $data['experiments'],
          'placeholder' => 'Select an experiment',
          'attr' => array(
            'class' => 'chosen',
          )
        ))
        ->add("account", \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
          'class' => 'AppBundle:Accounts',
          'placeholder' => 'Select an Account',
          'query_builder' => function (\AppBundle\Repository\AccountsRepository $ar) {
            return $ar->createQueryBuilder('a')
                ->select('a, c')
                ->leftJoin('a.client', 'c')
            ;
          },
          'group_by' => function($account) {
            if ($account->getClient()) {
              return $account->getClient()->getName();
            }
            return "N/A";
          },
          'attr' => array(
            'class' => 'chosen',
          )
        ))
        ->add('Yalla', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
          'attr' => array(
            'class' => 'btn btn-success float-right',
          )
        ))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $formData = $form->getData();
      $rateCalculator = $this->get('myapp.rate.calculator');

      $data['result']['experiment'] = $formData['experiments'];
      $data['result']['account'] = $formData['account'];
      $data['result']['tasks'] = $this->getDoctrine()->getRepository('AppBundle:Tasks')->findByAccountNoWorklog($data['result']['account']);
      $data['result']['price'] = $rateCalculator->tasks($data['result']['tasks']);
    }

    return $this->render('AppBundle:Experiments:accounts.html.twig', array(
          "form" => $form->createView(),
          "data" => $data,
    ));
  }

}
