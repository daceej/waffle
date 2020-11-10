<?php

namespace Waffle\Model\Output;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

// @todo: Is there a better name for this?
// @todo: Should this be a helper instead of a model?

/**
 * Utility class for running processes and displaying formatted output.
 *
 * @package Waffle\Model\Output
 */
class Runner
{
    
    /**
     *
     */
    public function __construct()
    {
    }
    
    /**
     *
     * @param SymfonyStyle $io
     * @param $message
     * @param $command
     * @return string
     */
    public static function message(SymfonyStyle $io, $message, $command)
    {
        $io->section($message);
        if (is_string($command)) {
            $io->writeln($command);
        } elseif ($command instanceof Process) {
            $io->writeln($command->getCommandLine());
        }
        $io->newLine();
        
        $output = Runner::getOutput($command);
        $io->writeln($output);
        
        return $output;
    }
    
    public static function getOutput($command)
    {
        $process = null;
        if (is_string($command)) {
            $process = Process::fromShellCommandline($command);
        } elseif ($command instanceof Process) {
            $process = $command;
        }
        
        $process->run();
        $output = $process->getOutput();
        if (!empty($output)) {
            return $output;
        }
        
        // We didn't get anything back, so let's check some things.
        $output = $process->getErrorOutput();
        if (!empty($output)) {
            return $output;
        }
        
        $output = $process->getExitCodeText();
        if (!empty($output)) {
            return $output;
        }
        
        return 'NO OUTPUT';
    }
}
