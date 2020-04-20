<?php

namespace LibAuth\Tokens;

use LibAuth\Core\DataCore;

/**
 * Base Token
 *
 * @property string $oauth_token
 * @property string $oauth_token_secret
 */
class Token extends DataCore {

  const FIELDS = [
    'oauth_token' => 'string',
    'oauth_token_secret' => 'string',
  ];

}
