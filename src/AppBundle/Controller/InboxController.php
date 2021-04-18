<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Inbox controller.
 *
 * @Route("/inbox")
 */
class InboxController extends Controller
{



    /**
     * Get Inbox Tasks and render view
     *
     * @param string $taskListName
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/getTasks/{taskListName}", name="inbox_get_tasks")
     */
    public function getTasksAction($taskListName)
    {
        $em = $this->getDoctrine()->getManager();
        $inboxTasks = [];
        switch ($taskListName) {
            case 'focus':
                $inboxTasks = $em->getRepository('AppBundle:Tasks')->focusList();
                break;
            case 'urgent':
                break;
            case 'completedToday':
                $inboxTasks = $em->getRepository('AppBundle:Tasks')->getCompletedToday();

                break;
            default:
                // @TODO: Not found handler
                $taskList = $em->getRepository('AppBundle:TaskLists')->findOneBy(['name' => $taskListName]);
                $inboxTasks = $em->getRepository('AppBundle:Tasks')->focusByTasklist($taskList);
                break;
        }
        return $this->render("AppBundle:inbox:inboxTasks.html.twig", [
            'inboxTasks' => $inboxTasks
        ]);
    }

}
