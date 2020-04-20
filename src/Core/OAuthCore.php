<?php

namespace LibAuth\Core;

use LibAuth\Exceptions\Exception;
use LibAuth\Params\Params;
use LibAuth\Tokens\AccessToken;
use LibAuth\Tokens\RequestToken;
use LibAuth\Tokens\Token;
use ReflectionException;

/**
 * OAuthCore
 *
 * This is the base class for all versions of OAuth classes
 *
 * @method AccessToken getAccessToken(array $params = [])
 *  Get Access Token
 *
 * @method RequestToken getRequestToken(array $params = [])
 *  Get Request Token
 */
class OAuthCore extends RequestHandler {

  protected $id;

  protected $secret;

  const AUTH_TYPE_NONE = 0;
  const AUTH_TYPE_URI = 1;
  const AUTH_TYPE_FORM = 2;
  const AUTH_TYPE_AUTHORIZATION = 3;

  /**
   * Current Token
   *
   * @var Token
   */
  private $token;

  /**
   * Authentication Type
   *
   * @var int
   */
  private $authType;

  function __construct($id, $secret) {
    $this->id = $id;
    $this->secret = $secret;
    parent::__construct();
  }

  /**
   * Set Current Token
   *
   * @param Token $token
   * @return void
   */
  public function setToken(Token $token) {
    $this->token = $token;
  }

  /**
   * Get Current Token
   *
   * @return Token
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Set Authentication Type
   *
   * @param integer $authType
   * @return void
   */
  public function setAuthType(int $authType) {
    $this->authType = $authType;
  }

  /**
   * Get Authentication Type
   *
   * @return int
   */
  public function getAuthType() {
    return $this->authType;
  }

  private function process($name, array $params = []) {
    $className = $this->reference()->name;

    try {
      $method = $this->reference()->getMethod($name);

      if (!$method->isProtected()) {
        throw new Exception("'{$className}::{$name}' must be protected!");
      }

      $method->setAccessible(true);

      $parameters = $method->getParameters();

      if (!count($parameters)) {
        throw new Exception("'{$className}::{$name}' must have at least 1 argument");
      } else {
        $parameter = $parameters[0];
        $type = $parameter->getType();
        $typeHint = call_user_func([$type, 'getName']);

        if (!is_subclass_of($typeHint, Params::class)) {
          throw new Exception("Argument 1 of '{$className}::{$name}' must be of type '".Params::class."'");
        } else {
          $params = new $typeHint($params);
          return $method->invoke($this, $params);
        }
      }
    } catch (ReflectionException $e) {
      throw new Exception($e->getMessage());
      // throw new Exception("'{$className}::{$name}' does not exist!");
      // throw new Exception("'{$className}::{$name}' not implemented!");
    }
  }

  /**
   * Magic function for generic calls
   *
   * @param string $name
   * @param array $arguments
   * @return void
   *
   * @ignore
   */
  public function __call($name, $arguments) {
    $handler = 'handle' . ucfirst($name);
    return $this->process($handler, ...$arguments);
  }

}
