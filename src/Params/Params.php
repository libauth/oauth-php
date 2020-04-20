<?php

namespace LibAuth\Params;

use LibAuth\Core\DataCore;

/**
 * Base OAuth Params
 *
 * @property string $url
 *  URL of the request
 */
class Params extends DataCore {

  const FIELDS = [
    'url' => 'string'
  ];

}
