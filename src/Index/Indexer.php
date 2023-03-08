<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Index;

use Metfan\LibSearch\Client\ClientBuilderInterface;

class Indexer
{
    public function __construct(private ClientBuilderInterface $clientBuilder)
    {
    }

    public function index(string $index, $documentId, array $body): void
    {
        $client = $this->clientBuilder->build();
        $client->index(
            [
                'index' => $index,
                'id' => $documentId,
                'body' => $body,
            ]
        );
    }

    public function deindex(string $index, int|string $documentId): void
    {
        $client = $this->clientBuilder->build();
        $client->delete(
            [
                'index' => $index,
                'id' => $documentId,
            ]
        );
    }
}
