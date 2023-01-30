<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Exception;


class IndexDeletionUnauthorizedException extends \LogicException
{
    public function __construct(string $indexName)
    {
        parent::__construct(sprintf('Index named: "%s" has an alias. It can\'t be deleted.', $indexName));
    }
}
