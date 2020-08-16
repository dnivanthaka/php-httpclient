<?php

namespace Dinusha\Http;

use Dinusha\Exceptions\HttpClientException;

use Exception;
use JsonException;

/**
 * This is a class to make http requests to REST based web services
 * 
 * @author D.N. Amerasinghe
 */
class HttpClient
{
    private $uri;
    private $headers;
    private $method;
    private $data;
    private $stream;

    /**
     * Construct a HttpClient instance
     * 
     */
    public function __construct($uri = null, $method = 'GET', $headers = [])
    {
        $this->uri = $uri;
        $this->headers = $headers;
        $this->method = $method;

        if (!array_key_exists('Content-Type', $this->headers)) {
            $this->headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
    }

    /**
     * Sets the request method
     * 
     * @return HttpClient
     */
    public function withMethod(string $method): HttpClient
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Sets headers in the request
     * 
     * @return HttpClient
     */
    public function withHeader(string $name, string $value): HttpClient
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Sets request parameters
     * 
     * @return HttpClient
     */
    public function withParams(array $data): HttpClient
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Sets the URI for the request
     * 
     * @return HttpClient
     */
    public function withUri(string $uri): HttpClient
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Builds the required request headers and
     * returns it as a string
     * 
     * @return string
     */
    private function buildHeaders(): string
    {
        $data = '';

        foreach ($this->headers as $key => $value) {
            $data .= $key . ": " . $value . "\r\n";
        }

        return $data;
    }

    /**
     * Builds request content as json
     * 
     * @return string
     */
    private function buildContents(): string
    {
        $data = '';

        try {
            if (is_array($this->data)) {
                $data = json_encode($this->data);
            }
        } catch (JsonException $e) {
            throw new HttpClientException($e->getMessage());
        }


        return $data;
    }

    /**
     * Makes the http request and returns a HttpResponse object
     * 
     * @return HttpResponse
     */
    public function makeRequest(): HttpResponse
    {
        try {
            $data = $this->buildContents();
            $headers = $this->buildHeaders();

            $context_options = array(
                'http' => array(
                    'method' => $this->method,
                    'header' => $headers
                        . "Content-Length: " . strlen($data) . "\r\n",
                    'content' => $data
                )
            );

            $context = stream_context_create($context_options);
            $this->stream = fopen($this->uri, 'r', false, $context);

            return new HttpResponse($this->stream);
        } catch (Exception $e) {
            throw new HttpClientException($e->getMessage());
        }
    }

    /**
     * Returns a string representation of the response content
     * 
     * @return string
     */
    public function __toString(): string
    {
        try {
            $contents = stream_get_contents($this->stream);
            return $contents;
        } catch (Exception $e) {
        }
    }
}
