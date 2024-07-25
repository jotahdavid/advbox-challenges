<?php

namespace JotahDavid\RssCrawler;

class Response
{
    public readonly string $body;

    public readonly ?string $contentType;

    public function __construct(?string $body = null, ?string $contentType = null)
    {
        $this->body = $body ?? '';
        $this->contentType = $contentType;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }
}
