<?php

namespace LibAuth;

use LibAuth\Core\OAuthCore;
use LibAuth\Exceptions\InvalidVersionException;
use LibAuth\Tokens\RequestToken;
use LibAuth\Versions\OAuth_1_0;
use LibAuth\Versions\OAuth_2_0;
use LibAuth\Versions\OAuth_2_1;

/**
 * OAuth Class
 *
 * @method RequestToken getRequestToken(string $url, string $callback = NULL)
 */
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
    if (!array_key_exists($version, self::VERSIONS)) {
      $supported = array_keys(self::VERSIONS);
      $lastSupported = array_pop($supported);

      if (count($supported)) {
        $supported = implode(", ", $supported);
        $supported = implode(" and ", $lastSupported);
      } else {
        $supported = $lastSupported;
      }

      $message = "Version {$version} is currently not supported. ";
      $message .= "Supported versions are {$supported}";
      throw new InvalidVersionException($message);
    }

    $this->version = $version;

    $versionClass = self::VERSIONS[$version];
    $this->instance = new $versionClass($id, $secret);
  }

  public function getVersion() {
    return $this->version;
  }

  public function __call($name, $arguments) {
    return $this->instance->{$name}(...$arguments);
  }

}
