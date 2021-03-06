<?php

namespace Waffle\Model\Cli\Runner;

use Exception;
use Symfony\Component\Process\Process;
use Waffle\Helper\CliHelper;
use Waffle\Model\Cli\BaseCliRunner;
use Waffle\Model\Cli\Factory\GitCommandFactory;
use Waffle\Model\Context\Context;
use Waffle\Model\IO\IOStyle;

class Git extends BaseCliRunner
{

    /**
     * @var GitCommandFactory
     */
    private $gitCommandFactory;

    /**
     * @var CliHelper
     */
    private $cliHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param IOStyle $io
     * @param GitCommandFactory $gitCommandFactory
     * @param CliHelper $cliHelper
     *
     */
    public function __construct(
        Context $context,
        IOStyle $io,
        GitCommandFactory $gitCommandFactory,
        CliHelper $cliHelper
    ) {
        $this->gitCommandFactory = $gitCommandFactory;
        $this->cliHelper = $cliHelper;
        parent::__construct($context, $io);
    }

    /**
     * Adds all pending changes to index.
     *
     * @return Process
     * @throws Exception
     */
    public function addAll(): Process
    {
        $command = $this->gitCommandFactory->create(['add', '-A']);
        return $command->getProcess();
    }

    /**
     * Check git status.
     *
     * @return Process
     * @throws Exception
     */
    public function statusShort(): Process
    {
        $command = $this->gitCommandFactory->create(['status', '--short']);
        return $command->getProcess();
    }

    /**
     * Check if current git repo has any pending uncommitted changes.
     *
     * @return bool
     * @throws Exception
     */
    public function hasPendingChanges(): bool
    {
        $process = $this->statusShort();
        $process->run();
        return !empty($process->getOutput());
    }

    /**
     * Commit staged changes.
     *
     * @param string $message
     *
     * @return Process
     * @throws Exception
     */
    public function commit(string $message): Process
    {
        if (empty($message)) {
            throw new Exception('Git commit message is required.');
        }

        $command = $this->gitCommandFactory->create(['commit', "--message={$message}"]);
        return $command->getProcess();
    }

    /**
     * Check if a branch exists locally.
     *
     * @param string $branch
     *
     * @return bool
     * @throws Exception
     */
    public function branchExists(string $branch): bool
    {
        if (empty($branch)) {
            return false;
        }

        $process = $this->branchList($branch);
        $output = $this->cliHelper->getOutput($process);

        return strpos($output, $branch) !== false;
    }

    /**
     * Get a list of local git branches.
     *
     * @param string|null $branch
     *
     * @return Process
     * @throws Exception
     */
    public function branchList(string $branch = null): Process
    {
        $args = ['branch', '--list'];
        if (!empty($branch)) {
            $args[] = $branch;
        }

        $command = $this->gitCommandFactory->create($args);
        return $command->getProcess();
    }

    /**
     * Checkout a branch.
     *
     * @param string $branch
     * @param bool $isNew
     *
     * @return Process
     * @throws Exception
     */
    public function checkout(string $branch, bool $isNew = false): Process
    {
        if (empty($branch)) {
            throw new Exception('Git branch name is required.');
        }

        $args = ['checkout'];
        if ($isNew) {
            $args[] = '-b';
        }
        $args[] = $branch;

        $command = $this->gitCommandFactory->create($args);
        return $command->getProcess();
    }

    /**
     * Fetch from upstreams.
     *
     * @return Process
     * @throws Exception
     */
    public function fetch(): Process
    {
        $command = $this->gitCommandFactory->create(['fetch']);
        return $command->getProcess();
    }

    /**
     * Check if there are pending commits that should be pulled down.
     *
     * @param $upstream
     * @param $branch
     *
     * @return bool
     */
    public function hasUpstreamPending($upstream, $branch): bool
    {
        $command = $this->gitCommandFactory->create(['log', "HEAD..{$upstream}/{$branch}", '--oneline']);
        $process = $command->getProcess();
        $output = $this->cliHelper->getOutput($process);
        return !empty(trim($output));
    }

    /**
     * Get the name of the current branch.
     *
     * @return string
     * @throws Exception
     */
    public function getCurrentBranch(): string
    {
        $command = $this->gitCommandFactory->create(['branch', '--show-current']);
        $process = $command->getProcess();
        return trim($this->cliHelper->getOutput($process));
    }

    /**
     * Do a hard reset on git repo.
     *
     * @return Process
     */
    public function resetHard(): Process
    {
        $command = $this->gitCommandFactory->create(['reset', '--hard']);
        return $command->getProcess();
    }

    /**
     * Clean the git repo.
     *
     * @return Process
     */
    public function clean(): Process
    {
        $command = $this->gitCommandFactory->create(['clean', '-fd']);
        return $command->getProcess();
    }
}
