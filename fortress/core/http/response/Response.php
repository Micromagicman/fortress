<?php

namespace fortress\core\http\response;

use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface {

  private $responsePhrases = [
    100 => "Continue",
    101 => "Switching Protocols",
    200 => "OK",
    201 => "Created",
    202 => "Accepted",
    203 => "Non-Authoritative Information",
    204 => "No Content",
    205 => "Reset Content",
    206 => "Partial Content",
    300 => "Multiple Choices",
    301 => "Moved PerManently",
    302 => "Found",
    303 => "See Other",
    304 => "Not Modified",
    305 => "Use Proxy"
  ];

  
}
