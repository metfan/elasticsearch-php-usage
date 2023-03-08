<?php
declare(strict_types=1);

namespace Metfan\LibSearch\App;

interface PostProvider
{
    /**
     * @return Post[]
     */
    public function findAll(): array;

    /**
     * @return Int[]
     */
    public function findIds(int $offset, int $limit): array;

    public function findById(int $articleId): ?Post;
}
