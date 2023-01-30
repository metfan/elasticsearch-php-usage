<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Command;

use Metfan\LibSearch\Index\IndexCreator;
use Metfan\LibSearch\Index\IndexSwitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateIndexCommand extends Command
{
    public function __construct(private IndexCreator $indexCreator, private IndexSwitcher $switcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('metfan:es:create-index')
            ->setDescription('Create index in elasticsearch')
            ->addOption(
                'autoswitch',
                null,
                InputOption::VALUE_NONE,
                'Add this option to switch alias on the new index right after creation'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $indexName = $this->indexCreator->createIndex();
        $output->writeln(sprintf('<comment>New index <info>%s</info> created.</comment>', $indexName));

        if (true === $input->getOption('autoswitch')) {
            $this->switcher->switchIndex($indexName);
            $output->writeln('Alias have been switched to teh new index.');
        }

        return Command::SUCCESS;
    }
}
