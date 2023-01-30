<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Index;

use Metfan\LibSearch\Client\ClientBuilderInterface;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Webmozart\Assert\Assert;

class IndexSwitcher
{
    public function __construct(
        private ClientBuilderInterface $clientBuilder,
        private IndexNameGenerator $indexNameGenerator,
        private string $aliasName
    ) {
    }

    public function switchIndex(?string $indexName = null): string
    {
        $indices = $this->clientBuilder->build()->indices();

        // get all indices from indice pattern
        /** @var Elasticsearch $indicesAliasResponse */
        $indicesAliasResponse = $indices->getAlias(['index' => $this->indexNameGenerator->generateWildcardPattern()]);
        Assert::isInstanceOf($indicesAliasResponse, Elasticsearch::class);
        $indicesAlias = $indicesAliasResponse->asArray();


        if (null === $indexName) {
            ksort($indicesAlias);
            $indexName = (string) key(array_reverse($indicesAlias, true));
        }

        $aliasName = $this->aliasName;
        //extract all indices with alias
        $currentIndicesAlias = array_keys(
            array_filter(
                $indicesAlias,
                function (array $aliases) use ($aliasName) {
                    return is_array($aliases['aliases']) && isset($aliases['aliases'][$aliasName]);
                }
            )
        );

        $request = [
            'body' => [
                'actions' => [
                    ['add' => ['index' => $indexName, 'alias' => $aliasName]],
                ]
            ]
        ];
        foreach ($currentIndicesAlias as $current) {
            $request['body']['actions'][] = ['remove' => ['index' => $current, 'alias' => $aliasName]];
        }

        $indices->updateAliases($request);

        return $indexName;
    }
}
