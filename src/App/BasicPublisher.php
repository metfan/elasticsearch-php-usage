<?php
declare(strict_types=1);

namespace Metfan\LibSearch\App;

interface BasicPublisher
{
    public function publishMessage(Message $message, string $queue): void;
}
