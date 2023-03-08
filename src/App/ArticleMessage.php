<?php
declare(strict_types=1);

namespace Metfan\LibSearch\App;

class ArticleMessage implements Message
{
    public function __construct(public readonly int $articleId, public readonly ?string $indexName = null)
    {
    }
}
