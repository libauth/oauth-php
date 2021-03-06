<?php

namespace LibAuth;

use LibAuth\Core\OAuthCore;
use LibAuth\Exceptions\InvalidVersionException;
use LibAuth\Tokens\AccessToken;
use LibAuth\Tokens\RequestToken;
use LibAuth\Tokens\Token;
use LibAuth\Versions\OAuth_1_0;
use LibAuth\Versions\OAuth_2_0;
use LibAuth\Versions\OAuth_2_1;

/**
 * OAuth Class
 *
 * @method string getAppId()
 *  Get App ID also known as `consumer_key` or `api_key`
 *
 * @method void setToken(Token $token)
 *  Set Active Token
 *
 * @method Token getToken()
 *  Get Active Token
 *
 * @method void setAuthType(int $authType)
 *  Set Authentication Type
 *
 * @method int getAuthType()
 *  Get Authentication Type
 *
 * @method AccessToken getAccessToken(array $params = [])
 *  Get Access Token
 *
 * @method RequestToken getRequestToken(array $params = [])
 *  Get Request Token
 *
 * @method mixed request(string $method, string $path, array $options = [])
 *  Make an OAuth request
 *
 * @method mixed get(string $path, array $options = [])
 *  Make an OAuth GET request
 *
 * @method mixed post(string $path, array $options = [])
 *  Make an OAuth POST request
 *
 * @method mixed patch(string $path, array $options = [])
 *  Make an OAuth PATCH request
 *
 * @method mixed put(string $path, array $options = [])
 *  Make an OAuth PUT request
 *
 * @method mixed delete(string $path, array $options = [])
 *  Make an OAuth DELETE request
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

  /**
   * Instantiate OAuth
   *
   * @param string $id
   * @param string $secret
   * @param string $endpoint
   * @param string $version
   * @param boolean $sslVerify
   */
  function __construct($id, $secret, $endpoint, $version = '2.0', $sslVerify = true) {
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
    $this->instance = new $versionClass($id, $secret, $endpoint, $sslVerify);
  }

  /**
   * Get OAuth Version
   *
   * @return string
   */
  public function getVersion() {
    return $this->version;
  }

  public function __call($name, $arguments) {
    return $this->instance->{$name}(...$arguments);
  }

}
