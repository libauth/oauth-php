<?php

namespace LibAuth\Params;

/**
 * OAuth AccessToken Params
 *
 * @property string $oauth_token
 *  Temporary OAuth Token
 *
 * @property string $verifier
 *  OAuth Verifier
 */
class AccessTokenParams extends Params {

  const FIELDS = [
    'oauth_token' => 'string',
    'verifier' => 'string'
  ];

}
