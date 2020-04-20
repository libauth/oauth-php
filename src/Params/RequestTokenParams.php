<?php

namespace LibAuth\Params;

/**
 * OAuth RequestToken Params
 *
 * @property string $callback_url
 *  Callback URL
 */
class RequestTokenParams extends Params {

  const FIELDS = [
    'callback_url' => 'string'
  ];

}
