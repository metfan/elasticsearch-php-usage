<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Command;

use Metfan\LibSearch\App\PostProvider;
use Metfan\LibSearch\Index\IndexCreator;
use Metfan\LibSearch\Index\Indexer;
use Metfan\LibSearch\Index\IndexSwitcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexingCommand extends Command
{
    public function __construct(
        private PostProvider $postProvider,
        private Indexer $indexer,
        private IndexCreator $indexCreator,
        private IndexSwitcher $indexSwitcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('metfan:es:index-sync')
            ->setDescription('Index documents in ES')
            ->addOption(
                'reset-index',
                null,
                InputOption::VALUE_NONE,
                'Create a new index before indexing and switch alias after indexing.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output);
        if ($input->getOption('reset-index')) {
            $indexName = $this->indexCreator->createIndex();
            $output->writeln(sprintf('<info>New index: %s created.</info>', $indexName));
        }

        $articles = $this->postProvider->findAll();
        $progressBar->start(count($articles));

        foreach ($articles as $article) {
            $this->indexer->index($indexName, $article->getId(), $article->toArray());
            $progressBar->advance();
        }

        $progressBar->finish();

        if ($input->getOption('reset-index') && isset($indexName)) {
            $this->indexSwitcher->switchIndex($indexName);
            $output->writeln(sprintf(
                    '<info>Alias switched to index: %s.</info>',
                    $indexName)
            );
        }

        return Command::SUCCESS;
    }
}
