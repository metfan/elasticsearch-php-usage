<?php
declare(strict_types=1);

namespace Metfan\LibSearch\App;

interface Post
{
    public function getId(): int;

    public function toArray(): array;
}
