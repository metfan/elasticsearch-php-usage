<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Index;

use Metfan\LibSearch\Client\ClientBuilderInterface;
use Metfan\LibSearch\Request\RequestForgery;

class IndexCreator
{
    public function __construct(
        private ClientBuilderInterface $builder,
        private RequestForgery $forgery,
        private IndexNameGenerator $indexNameGenerator
    ) {
    }

    public function createIndex(): string
    {
        $client = $this->builder->build();

        $indexName = $this->indexNameGenerator->generateName();
        $request = $this->forgery->forgeRequest([], $indexName);
        $client->indices()->create($request);

        return $indexName;
    }
}
