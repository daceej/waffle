<?php

namespace Waffle\Model\Config\Item;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Waffle\Model\Config\BaseConfigItem;
use Waffle\Model\Config\ConfigItemInterface;

class Upstreams extends BaseConfigItem
{
    /**
     * @var string
     *
     * The key for this config item.
     */
    public const KEY = 'upstreams';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            self::KEY,
            [
                ConfigItemInterface::SCOPE_PROJECT,
                ConfigItemInterface::SCOPE_LOCAL,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        // TODO -- This is a scalar for backwards compatability (for now).
        $nodeBuilder = new NodeBuilder();
        return $nodeBuilder->scalarNode(self::KEY);
    }
}
