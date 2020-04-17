<?php

namespace LibAuth\Core;

use LibAuth\Tokens\Token;

class OAuthCore {

  protected $id;

  protected $secret;

  /**
   * Request Handler
   *
   * @var RequestHandler
   */
  private $handler;

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
    //
  }

  /**
   * Get Access Token
   *
   * @return Token
   */
  public function getAccessToken() {
    //
  }

  /**
   * Get Request Token
   *
   * @return Token
   */
  public function getRequestToken() {
    //
  }

}
