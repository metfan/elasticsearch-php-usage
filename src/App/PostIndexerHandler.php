<?php
declare(strict_types=1);

namespace Metfan\LibSearch\App;

use Metfan\LibSearch\Index\Indexer;

class PostIndexerHandler implements ConsumerHandler
{
    public function __construct(
        private PostProvider $itemProvider,
        private Indexer $indexer,
        private string $indexAlias
    ) {
    }

    public function process(Message $message): void
    {
        if (!$message instanceof ArticleMessage) {
            return;
        }

        try {
            $item = $this->itemProvider->findById($message->articleId);
            $this->indexer->index(
                $message->indexName ?? $this->indexAlias,
                $item->getId(),
                $item->toArray()
            );
        } catch (NoResultException) {
            $this->indexer->deindex(
                $message->indexName ?? $this->indexAlias,
                $message->articleId
            );
        }
    }
}
