<?php

namespace LibAuth\Exceptions;

use Exception as GlobalException;

class Exception extends GlobalException {

  function __construct($message, $code = 500) {
    parent::__construct($message, $code);
  }

}
