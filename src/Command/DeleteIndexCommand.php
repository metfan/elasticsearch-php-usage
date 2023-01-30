<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Command;

use Metfan\LibSearch\Exception\IndexDeletionFailedException;
use Metfan\LibSearch\Exception\IndexDeletionUnauthorizedException;
use Metfan\LibSearch\Exception\IndexNotFoundException;
use Metfan\LibSearch\Index\IndexNameGenerator;
use Metfan\LibSearch\Index\IndexRemover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Webmozart\Assert\Assert;

class DeleteIndexCommand extends Command
{
    public function __construct(
        private IndexRemover $indexRemover,
        private IndexNameGenerator $indexNameGenerator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('metfan:es:clean-index')
            ->setDescription('Interactively delete index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(
            "<info>Info\nYou are about to delete index from Elasticsearch\nFirst review the list of existing indices</info>"
        );
        $indexList = new Table($output);
        $indexList->setHeaders(['Index', 'with alias']);
        foreach ($this->indexRemover->getIndicesList($this->indexNameGenerator->generateWildcardPattern()) as $indexConfig) {
            $indexList->addRow([$indexConfig['name'], true === $indexConfig['with_alias'] ? '*' : '']);
        }
        $indexList->render();

        $questionHelper = $this->getHelper('question');
        Assert::isInstanceOf($questionHelper, QuestionHelper::class);;
        $question = new Question('Choose index to delete. press Enter to stop: ', 'none');

        do {
            $reply = $questionHelper->ask($input, $output, $question);
            if ('none' === $reply) {
                break;
            }

            try {
                $this->indexRemover->remove($reply);
            } catch (IndexNotFoundException|IndexDeletionUnauthorizedException|IndexDeletionFailedException $exception) {
                $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
                continue;
            }

            $output->writeln('<comment>Index deleted</comment>');
        } while (true);

        return Command::SUCCESS;
    }
}
