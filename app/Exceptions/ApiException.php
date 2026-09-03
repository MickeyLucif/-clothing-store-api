<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
   public function __construct(
       public readonly string $errorCode,
       string $message,
       public readonly int $statusCode,
   ) {
       parent::__construct($message);
   }
}
