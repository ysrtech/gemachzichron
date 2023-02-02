<?php

namespace App\Gateways\Rotessa\Traits;

use App\Exceptions\RotessaApiException;

trait HttpClientWrapper
{
    /**
     * @param string $url
     * @param $query
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function get(string $url, $query = null)
    {
        return $this->httpRequest->get($url, $query)->throw($this->exceptionThrowerCallback());
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function post(string $url, array $data)
    {
        return $this->httpRequest->post($url, $data)->throw($this->exceptionThrowerCallback());
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function patch(string $url, array $data)
    {
        return $this->httpRequest->patch($url, $data)->throw($this->exceptionThrowerCallback());
    }

    /**
     * @param string $url
     * @param array $data
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
     * @throws RotessaApiException
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function delete(string $url)
    {
        return $this->httpRequest->delete($url)->throw($this->exceptionThrowerCallback());
    }

    private function exceptionThrowerCallback(): \Closure
    {
        return function ($response, $e) {
            if ($e->getCode() == 422) {
                $message = $response->collect('errors')->reduce(fn($carry, $item) => $carry . ($carry ? ", " : '') . $item['error_message'] ?? '');
                throw new RotessaApiException($message);
            }
            else {
                throw $e;
            }
        };
    }
}
