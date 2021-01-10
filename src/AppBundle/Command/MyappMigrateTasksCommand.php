<?php

namespace AppBundle\Command;

use AppBundle\Definition\Types;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Item;
use AppBundle\Entity\Task;

class MyappMigrateTasksCommand extends Command
{
    protected static $defaultName = 'myapp:migrate:tasks';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->entityManager;

        // A. Access repositories
        $repo = $em->getRepository("AppBundle:Tasks");

        // B. Search using regular methods.
        $allTasks = $repo->findAll();
        foreach ($allTasks as $task) {
            $item = new Item();
            $item
                ->setTitle($task->getTask())
                ->setPriority($task->getPriority() + $task->getUrgency())
                ->setSort($task->getOrder())
                ->setType(Types::TASK);
            if ($task->getCreatedAt()->getTimestamp() > 0) {
                $item->setCreatedAt($task->getCreatedAt());
            }else{
                $item->setCreatedAt(new \DateTime());
            }
            if ($task->getUpdatedAt()->getTimestamp() > 0) {
                $item->setUpdatedAt($task->getUpdatedAt());
            }
            $em->persist($item);
            $taskItem = new Task();
            $taskItem
                ->setItem($item)
                ->setCompletedAt($task->getCompletedAt())
                ->setDue($task->getEta())
                ->setDuration($task->getDuration())
                ->setEta($task->getEta())
                ->setIsCompleted($task->getCompleted())
                ->setSizing($task->getEst())
                ->setTaskList($task->getTaskList());
            $em->persist($taskItem);
        }
        $em->flush();

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
