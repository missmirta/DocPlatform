<?php

declare(strict_types=1);

namespace DocPlatform\Http;

final class Response
{
    private function __construct(
        public readonly int $status,
        public readonly array $data,
    ) {}

    public static function json(array $data, int $status = 200): self
    {
        return new self($status, $data);
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($this->data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
