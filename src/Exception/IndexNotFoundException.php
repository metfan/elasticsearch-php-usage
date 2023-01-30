<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Exception;

class IndexNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $indexName)
    {
        parent::__construct(sprintf('Index named: "%s" doesn\'t exist.', $indexName));
    }

}
