<?php

namespace Waffle\Model\Drush;

use Symfony\Component\Process\Process;

class CacheClear extends DrushCommand
{

    public function __construct()
    {
        trigger_error('Warning: Drush command classes have been deprecated. Use DrushCommandRunner instead.');
        parent::__construct(['cr']);
    }
}
