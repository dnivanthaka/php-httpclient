<?php

namespace Dinusha\Exceptions;

/**
 * Exception class used in the client
 */
class HttpClientException extends \Exception
{
    private $error;

    public function __construct($e){
        $this->error = $e;
    }

}
