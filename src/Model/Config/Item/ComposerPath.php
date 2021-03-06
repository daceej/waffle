<?php

namespace Waffle\Model\Config\Item;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Waffle\Model\Config\BaseConfigItem;
use Waffle\Model\Config\ConfigItemInterface;

class ComposerPath extends BaseConfigItem
{
    /**
     * @todo Consider removing composer_path as a config option. For now, this
     * is included for backwards compatabilty.
     *
     * Similar to the drush runner class, I think the composer runner class
     * would be a good candidate to absorb this feature.
     */

    /**
     * @var string
     *
     * The key for this config item.
     */
    public const KEY = 'composer_path';

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
        $nodeBuilder = new NodeBuilder();
        return $nodeBuilder->scalarNode(self::KEY);
    }
}
