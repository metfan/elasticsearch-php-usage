<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Command;

use Metfan\LibSearch\Index\IndexSwitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SwitchIndexCommand extends Command
{
    public function __construct(private IndexSwitcher $switcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('metfan:es:switch-index')
            ->setDescription('Switch index in elasticsearch')
            ->addOption('index-name', null, InputOption::VALUE_REQUIRED, 'Name of the index to use', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $indexName = $input->getOption('index-name');
        if ($indexName !== null && !is_string($indexName)) {
            $output->writeln('<error>index-name option should be null or string</error>');
            return Command::FAILURE;
        }

        $indexName = $this->switcher->switchIndex($indexName);
        $output->writeln(sprintf('<comment>Alias switched on index: <info>%s</info>.</comment>', $indexName));

        return Command::SUCCESS;
    }
}
