<?php

declare(strict_types=1);

namespace DocPlatform\Http;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $body,
        public readonly array $query,
    ) {}

    public static function fromGlobals(): self
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $body = [];
            $raw  = file_get_contents('php://input');
            if ($raw !== false && $raw !== '') {
                $body = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            }
        } else {
            $body = $_POST;
        }
        return new self(
            method: strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            path:   parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/',
            body:   $body,
            query:  $_GET,
        );
    }
}
