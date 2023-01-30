<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Client;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder as EsClientBuilder;

class ClientBuilder implements ClientBuilderInterface
{
    private ?Client $client = null;

    public function __construct(private string $host, private string $port)
    {
    }

    public function build(): Client
    {
        if (null ==! $this->client) {
            return $this->client;
        }

        $this->client = EsClientBuilder::create()
            ->setHosts([$this->host.':'.$this->port])
            ->build();

        return $this->client;
    }
}
