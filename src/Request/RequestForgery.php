<?php
declare(strict_types=1);

namespace Metfan\LibSearch\Request;

use Twig\Environment;

class RequestForgery
{
    public function __construct(
        private Environment $templating,
        private string $aliasName,
        private string $templateName
    ) {
    }

    /**
     * @param array<string, mixed> $tplParams
     *
     * @return array{index: string, body: string}
     */
    public function forgeRequest(array $tplParams = [], ?string $indexName = null): array
    {
        return [
            'index' => $indexName ?? $this->aliasName,
            'body' => $this->templating->render($this->templateName, $tplParams),
        ];
    }
}
