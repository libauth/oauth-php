<?php

namespace LibAuth\Params;

/**
 * OAuth RequestParams Params
 *
 * @property mixed $data
 *  Callback URL
 *
 * @property mixed $signed
 *  Specify if the request should be signed
 */
class RequestParams extends Params {

  const FIELDS = [
    'data' => 'mixed',
    'signed' => 'boolean'
  ];

}
