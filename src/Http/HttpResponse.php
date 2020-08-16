<?php

namespace Dinusha\Http;

use Dinusha\Exceptions\HttpClientException;
use Exception;

/**
 * This class is used by the HttpClient for parsing the server response
 * 
 * @author D.N. Amerasinghe
 */
class HttpResponse
{
    private $headers;
    private $content;
    private $contentType;
    private $statusCode;
    private $stream;

    /**
     * Construct a HttpResponse
     */
    public function __construct($stream)
    {
        $this->stream = $stream;

        $this->parse();
    }

    /**
     * Parse the response
     * 
     * @return void
     */
    private function parse() //: void
    {
        $this->parseHeaders();
        $this->parseContent();
    }

    /**
     * Returns the content type
     * 
     * @return content type as string
     */
    private function getContentType(): string
    {
        $ret = 'none';

        if (stripos($this->contentType, 'text/plain') !== false) {
            $ret = 'text';
        } else if (stripos($this->contentType, 'json') !== false) {
            $ret = 'json';
        }

        return $ret;
    }

    /**
     * Parse the response headers
     * 
     * @return void
     */
    private function parseHeaders() //: void
    {
        try {
            $this->headers = stream_get_meta_data($this->stream);
            $status = explode(' ', $this->headers['wrapper_data'][0]);

            $this->statusCode = $status[1];

            foreach ($this->headers['wrapper_data'] as $header) {
                if (stripos($header, 'Content-Type') !== false) {
                    $type = explode(' ', $header);
                    $this->contentType = $type[1];
                }
            }

            if ($this->statusCode != 200) {
                throw new HttpClientException("Incorrect response " . $status[2] . ' Code: ' . $status[1]);
            }
        } catch (Exception $e) {
            throw new HttpClientException("Invalid response format");
        }
    }

    /**
     * Parse the response body content
     * 
     * @return void
     */
    private function parseContent() //: void
    {
        try {
            $contents = stream_get_contents($this->stream);

            if ($this->getContentType() == 'json') {
                $this->content = json_decode($contents, true);
            } else if ($this->getContentType() == 'text') {
                $this->content[0] = $contents;
            }
        } catch (Exception $e) {
            throw new HttpClientException('Invalid response format ' . $e->getMessage());
        }
    }

    /**
     * Returns the response content as an array
     * 
     * @return array
     */
    public function getResponseArray(): array
    {
        return $this->content;
    }
}
