<?php

namespace LibAuth\Tokens;


class AccessToken extends Token {

  const FIELDS = [
    'oauth_token' => 'string',
    'oauth_token_secret' => 'string'
  ];

}
