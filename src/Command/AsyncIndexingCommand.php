<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Command;

use Metfan\LibSearch\App\ArticleMessage;
use Metfan\LibSearch\App\BasicPublisher;
use Metfan\LibSearch\App\PostProvider;
use Metfan\LibSearch\Index\IndexCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AsyncIndexingCommand extends Command
{
    public function __construct(
        private PostProvider $itemProvider,
        private BasicPublisher $publisher,
        private IndexCreator $indexCreator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('metfan:es:index-async')
            ->setDescription('Index doc in elasticsearch')
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_REQUIRED,
                'id of the real item to index',
                null
            )
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'A file containing 1 ID per line',
                null
            )
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'index all items')
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Offset', '0')
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limit', '100')
            ->addOption(
                'create-index',
                null,
                InputOption::VALUE_NONE,
                'Create a new index before indexing.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('create-index')) {
            $indexName = $this->indexCreator->createIndex();
            $output->writeln(sprintf('<info>New index: %s created.</info>', $indexName));
        }

        $nbMessage = 0;
        foreach ($this->retrieveIdsToIndex($input) as $id) {
            $this->publisher->publishMessage(
                new ArticleMessage($id, $indexName ?? null),
                'article_queue'
            );
            $nbMessage++;
        }
        $output->writeln(sprintf(
            '<comment>Published <info>%d</info> message(s) to process.</comment>',
            $nbMessage
        ));

        return Command::SUCCESS;
    }

    private function retrieveIdsToIndex(InputInterface $input): iterable
    {
        if (null !== $objectId = $input->getOption('id')) {
            yield (int) $objectId;
        } elseif (null !== $file = $input->getOption('file')) {
            $handle = new \SplFileObject($file);

            foreach ($handle as $line) {
                if (!is_string($line)) {
                    return;
                }
                $line = trim($line);
                if (!empty($line)) {
                    yield (int) $line;
                }
            }
        } elseif (true === $input->getOption('all')) {
            yield from $this->itemProvider->findIds(
                (int) $input->getOption('offset'),
                (int) $input->getOption('limit')
            );
        }
    }
}
