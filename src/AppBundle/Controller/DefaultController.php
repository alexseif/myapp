<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Tasks;
use AppBundle\Entity\TaskLists;
use AppBundle\Form\TasksType;
use AppBundle\Model\ActionItem;

class DefaultController extends Controller
{

  /**
   * 
   * @Route("/lists", name="lists_view")
   */
  public function listsAction()
  {
    $em = $this->getDoctrine()->getManager();
    $today = new \DateTime();

    $lists = $em->getRepository('AppBundle:TaskLists')->findBy(array('status' => 'start'));
    return $this->render("AppBundle:default:lists.html.twig", array(
          'today' => $today,
          'lists' => $lists,
    ));
  }

  /**
   * 
   * @Route("/lists/{id}/modal", name="list_show_modal", methods={"GET"})
   */
  public function listModalAction(TaskLists $taskList)
  {
    $tasks = $taskList->getTasks(false);
    $random = rand(0, $tasks->count() - 1);
    return $this->render("AppBundle:tasks:show_modal.html.twig", array(
          'task' => $tasks->get($random),
    ));
  }

  /**
   * 
   * @Route("/getBottomBarDetails", name="get_bottom_bar_details", methods={"GET"})
   */
  public function getBottomBarDetails()
  {
    return $this->render("::bottom-bar-details.html.twig");
  }

  /**
   * 
   * @Route("/menu", name="menu", methods={"GET"})
   */
  public function getMenuAction()
  {
    $menu = [
      "Menu" => [
        "menu"
      ],
      "Work Week" => [
        "workweek_show"
      ],
      "Account Transactions" => [
        "accounttransactions_index",
        "accounttransactions_new",
      ],
      "Accounting" => [
        "accounting_index",
        "accounting_account_page",
        "accounting_statements_page"
      ],
      "Accounts" => [
        "accounts_index",
        "accounts_new",
      ],
      "Backups" => [
        "backups",
        "backups_generate",
      ],
      "Clients" => [
        "client_index",
        "client_new",
      ],
      "Contracts" => [
        "contract_index",
        "contract_new",
      ],
      "Cost Of Life" => [
        "costoflife_index",
        "costoflife_new",
      ],
      "Currency" => [
        "currency_index",
        "currency_new",
      ],
      "Dashboard" => [
        "dashboard"
      ],
      "WorkArea" => [
        "workarea"
      ],
      "Dashboard TaskLists" => [
        "dashboardtasklists_index",
        "dashboardtasklists_new",
        "dashboardtasklists_add",
        "dashboardtasklists_remove",
      ],
      "Days" => [
        "days_index",
        "days_archive",
        "days_new",
      ],
      "Views" => [
        "beta",
        "focus",
        "singleTask",
        "lists_view",
        "get_bottom_bar_details"
      ],
      "Experiments" => [
        "experiments_index",
        "experiment_tasks",
        "experiment_accounts"
      ],
      "Focus" => [
        "focus_index",
        "focus_new",
      ],
      "Management" => [
        "management_index",
        "management_priority",
        "management_search_page",
      ],
      "Notes" => [
        "notes_index",
        "notes_new",
      ],
      "Objectives" => [
        "objective_index",
        "objective_new",
      ],
      "Rate" => [
        "rate_index",
        "rate_new",
      ],
      "Rcurr" => [
        "recurr_index",
        "recurr_new",
      ],
      "Reports" => [
        "reports_index",
        "reports_clients",
        "reports_diff",
        "reports_income",
        "reports_annual_income",
        "reports_tasks_years",
      ],
      "Service" => [
        "service_index",
        "service_new",
      ],
      "TaskList" => [
        "tasklists_index",
        "tasklists_new",
      ],
      "Tasks" => [
        "tasks_index",
        "tasks_search",
        "tasks_advanced",
        "tasks_new",
      ],
      "Things" => [
        "thing_index",
        "thing_new",
      ],
      "Default" => [
        "default",
      ],
      "WorkLog" => [
        "worklog_index",
        "completed_tasks",
        "worklog_new",
        "worklog_autolog",
        "worklog_unloggable",
        "worklog_autodelete",
      ],
      "FOS" => [
        "fos_user_security_login",
        "fos_user_security_check",
        "fos_user_security_logout",
        "fos_user_profile_show",
        "fos_user_profile_edit",
        "fos_user_registration_register",
        "fos_user_registration_check_email",
        "fos_user_registration_confirmed",
        "fos_user_resetting_request",
        "fos_user_resetting_send_email",
        "fos_user_resetting_check_email",
        "fos_user_change_password",
      ],
    ];
    ksort($menu);
    return $this->render("::menu.html.twig", [
          "menu" => $menu
    ]);
  }

}
