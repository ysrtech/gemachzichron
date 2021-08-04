<?php

namespace App\Gateways;

use App\Contracts\Formatter;
use App\Contracts\Gateway;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class AbstractGateway implements Gateway
{
    protected PendingRequest $httpRequest;
    protected string $baseUrl = '';
    protected array $defaultOptions = [];
    protected Formatter $formatter;

    public function __construct()
    {
        $this->httpRequest = Http::baseUrl($this->getBaseUrl())->withOptions($this->getDefaultOptions());
    }

    protected function config($key)
    {
        return config("gateways.{$this->getName()}.$key");
    }

    public function setBaseUrl(string $baseUrl): Gateway
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setDefaultOptions(array $defaultOptions): Gateway
    {
        $this->defaultOptions = $defaultOptions;

        return $this;
    }

    public function getDefaultOptions(): array
    {
        return $this->defaultOptions;
    }

    public function setFormatter(Formatter $formatter): AbstractGateway
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function format($output): array
    {
        return $this->formatter->formatOutput($output);
    }

    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
