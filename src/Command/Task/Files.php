<?php

namespace Waffle\Command\Task;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Waffle\Command\BaseTask;
use Waffle\Command\DiscoverableTaskInterface;
use Waffle\Model\Context\Context;
use Waffle\Model\IO\IOStyle;
use Waffle\Model\Site\Sync\SiteSyncFactory;
use Waffle\Traits\DefaultUpstreamTrait;

class Files extends BaseTask implements DiscoverableTaskInterface
{
    use DefaultUpstreamTrait;

    public const COMMAND_KEY = 'sync-files';

    /**
     * @var SiteSyncFactory
     */
    protected $siteSyncFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param IOStyle $io
     * @param SiteSyncFactory $siteSyncFactory
     */
    public function __construct(
        Context $context,
        IOStyle $io,
        SiteSyncFactory $siteSyncFactory
    ) {
        $this->siteSyncFactory = $siteSyncFactory;
        parent::__construct($context, $io);
    }

    protected function configure()
    {
        parent::configure();
        $this->setName(self::COMMAND_KEY);
        $this->setDescription('Pulls the files down from the specified upstream.');
        $this->setHelp('Pulls the files down from the specified upstream.');

        // Shortcuts would be nice, but there seems to be an odd bug as of now
        // when using dashes: https://github.com/symfony/symfony/issues/27333
        $this->addOption(
            'upstream',
            null,
            InputArgument::OPTIONAL,
            'The upstream environment to sync from.',
            $this->getDefaultUpstream()
        );

        // TODO Expand the help section.
        // TODO Dynamically load in the upstream options from the config file.
        // TODO Validate the opstream option from the config file (in help).
    }

    /**
     * {@inheritdoc}
     */
    protected function process(InputInterface $input)
    {
        $upstream = $input->getOption('upstream');
        $allowed_upstreams = $this->context->getUpstreams();

        // Ensure upstream is valid.
        if (!in_array($upstream, $allowed_upstreams)) {
            $this->io->error(
                sprintf('Invalid upstream: %s. Allowed upstreams: %s', $upstream, implode('|', $allowed_upstreams))
            );
            return Command::FAILURE;
        }

        $remote_alias = sprintf('@%s.%s:%%files/', $this->context->getAlias(), $upstream);

        try {
            $sync = $this->siteSyncFactory->getSiteSyncAdapter($this->context->getCms());
            $sync->syncFiles($remote_alias);
            $this->io->success('Files Sync');
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
