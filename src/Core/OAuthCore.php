<?php

namespace LibAuth\Core;

use LibAuth\Exceptions\NotImplementedException;
use LibAuth\Tokens\RequestToken;
use LibAuth\Tokens\Token;

class OAuthCore {

  protected $id;

  protected $secret;

  /**
   * Request Handler
   *
   * @var RequestHandler
   */
  protected $handler;

  function __construct($id, $secret) {
    $this->id = $id;
    $this->secret = $secret;
    $this->handler = new RequestHandler();
  }

  final protected function getRequestHandler() {
    return $this->handler;
  }

  /**
   * Set Token
   *
   * @param Token $token
   * @return void
   */
  public function setToken(Token $token) {
    throw new NotImplementedException(__METHOD__.' not imlemented!');
  }

  /**
   * Get Request Token
   *
   * @return Token
   */
  public function getRequestToken($url) {
    throw new NotImplementedException(__METHOD__.' not imlemented!');
  }

  /**
   * Get Access Token
   *
   * @return Token
   */
  public function getAccessToken(RequestToken $token) {
    throw new NotImplementedException(__METHOD__.' not imlemented!');
  }

}
