<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Index;


use Metfan\LibSearch\Client\ClientBuilderInterface;
use Metfan\LibSearch\Exception\IndexDeletionFailedException;
use Metfan\LibSearch\Exception\IndexDeletionUnauthorizedException;
use Metfan\LibSearch\Exception\IndexNotFoundException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Webmozart\Assert\Assert;

class IndexRemover
{
    /** @var array<string, array{name: string, with_alias: bool}>  */
    private array $indicesList = array();

    public function __construct(private ClientBuilderInterface $clientBuilder)
    {
    }

    /**
     * @return array<string, array{name: string, with_alias: bool}>
     */
    public function getIndicesList(string $indexPattern): array
    {
        $indices = $this->clientBuilder->build()->indices();
        $list = $indices->get(['index' => $indexPattern]);

        Assert::isInstanceOf($list, Elasticsearch::class);

        foreach ($list->asArray() as $indexName => $config) {
            if (isset($config['aliases']) and !empty($config['aliases'])) {
                $this->indicesList[$indexName] = ['name' => $indexName, 'with_alias' => true];
                continue;
            }

            $this->indicesList[$indexName] = ['name' => $indexName, 'with_alias' => false];
        }

        return $this->indicesList;
    }

    public function remove(string $indexName): void
    {
        if (empty($this->indicesList)) {
            $this->getIndicesList($indexName);
        }

        if (!isset($this->indicesList[$indexName])) {
            throw new IndexNotFoundException($indexName);
        }

        if (true === ($this->indicesList[$indexName]['with_alias'])) {
            throw new IndexDeletionUnauthorizedException($indexName);
        }

        $indices = $this->clientBuilder->build()->indices();
        $response = $indices->delete(['index' => $indexName]);

        Assert::isInstanceOf($response, Elasticsearch::class);

        if (200 !== $response->getStatusCode()) {
            throw new IndexDeletionFailedException($indexName);
        }
    }
}
