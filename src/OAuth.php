<?php

namespace LibAuth;

use LibAuth\Core\OAuthCore;
use LibAuth\Versions\OAuth_1_0;
use LibAuth\Versions\OAuth_2_0;
use LibAuth\Versions\OAuth_2_1;

class OAuth {

  const VERSIONS = [
    '1.0' => OAuth_1_0::class,
    '2.0' => OAuth_2_0::class,
    '2.1' => OAuth_2_1::class
  ];

  /**
   * OAuth Version
   *
   * @var string
   *
   * @ignore
   */
  private $version;

  /**
   * OAuth Version Instance
   *
   * @var OAuthCore
   *
   * @ignore
   */
  private $instance;

  function __construct($id, $secret, $version = '2.0') {

  }

  public function __call($name, $arguments) {
    //
  }

}
