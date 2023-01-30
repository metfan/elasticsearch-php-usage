<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Client;

use Elastic\Elasticsearch\Client;

interface ClientBuilderInterface
{
    public function build(): Client;
}
