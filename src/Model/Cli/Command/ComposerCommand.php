<?php

namespace Waffle\Model\Cli\Command;

use Waffle\Model\Cli\BaseCliCommand;
use Waffle\Model\Config\Item\Bin;
use Waffle\Model\Context\Context;

class ComposerCommand extends BaseCliCommand
{

    /**
     * {@inheritdoc}
     */
    public function __construct(Context $context, array $args)
    {
        $binary = $context->getBin(Bin::BIN_COMPOSER);
        array_unshift($args, $binary);
        parent::__construct($context, $args);
    }
}
