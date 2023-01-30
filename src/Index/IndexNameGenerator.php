<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Index;

use \DateTimeImmutable;
use \DateTimeZone;

class IndexNameGenerator
{

    public function __construct(private string $indexPattern)
    {
    }

    public function generateName(): string
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $replacement = [
            '<DATE>' => $now->format('Ymd'),
            '<TIME>' => $now->format('His'),
        ];

        return str_replace(array_keys($replacement), array_values($replacement), $this->indexPattern);
    }

    public function generateWildcardPattern(): string
    {
        $replacement = [
            '<DATE>' => '*',
            '<TIME>' => '*',
        ];

        return str_replace(array_keys($replacement), array_values($replacement), $this->indexPattern);
    }
}
