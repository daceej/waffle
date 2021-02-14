<?php

namespace Waffle\Model\Command;

use Waffle\Model\Command\CommandManager;
use Waffle\Model\Command\TaskManager;
use Waffle\Model\Command\RecipeManager;

/**
 * This class name is a bit of a misnomer. This class is responsible for
 * loading Symfony Console Commands in general, which includes Commands, Tasks,
 * and Recipes in regards to Waffle.
 */
class CommandLoader
{

    /**
     * @var CommandManager
     */
    private $commandManager;

    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @var RecipeManager
     */
    private $recipeManager;

    /**
     * Constructor
     *
     * @param CommandManager
     *   A CommandManager that has been preloaded with Waffle commands.
     * @param TaskManager
     *   A TaskManager that has been preloaded with Waffle tasks.
     * @param RecipeManager
     *   A RecipeManager that has been preloaded with Waffle recipes.
     */
    public function __construct(
        CommandManager $commandManager,
        TaskManager $taskManager,
        RecipeManager $recipeManager
    ) {
        $this->commandManager = $commandManager;
        $this->taskManager = $taskManager;
        $this->recipeManager = $recipeManager;
    }

    /**
     * getCommands
     *
     * Gets a list of all Waffle commands.
     *
     * @return Command[]
     */
    public function getCommands()
    {
        $commands = $this->commandManager->getCommands();
        $tasks = $this->taskManager->getCommands();
        $recipes = $this->recipeManager->getCommands();

        return array_merge($commands, $tasks, $recipes);
    }
}