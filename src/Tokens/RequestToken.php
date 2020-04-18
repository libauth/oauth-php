<?php

namespace LibAuth\Tokens;


/**
 * Request Token
 *
 * @property boolean $oauth_callback_confirmed
 * @property string $oauth_token
 * @property string $oauth_token_secret
 */
class RequestToken extends Token {

  const FIELDS = [
    'oauth_callback_confirmed' => 'boolean',
    'oauth_token' => 'string',
    'oauth_token_secret' => 'string'
  ];

}
