<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Exception;


class IndexDeletionFailedException extends \RuntimeException
{
    public function __construct(string $indexName)
    {
        parent::__construct(sprintf('Deletion of index named: "%s" failed.', $indexName));
    }
}
