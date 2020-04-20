<?php

namespace LibAuth\Tokens;


/**
 * Request Token
 *
 * @property boolean $oauth_callback_confirmed
 */
class RequestToken extends Token {

  const FIELDS = [
    'oauth_callback_confirmed' => 'boolean'
  ];

}
