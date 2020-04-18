<?php

use LibAuth\OAuth;
use LibAuth\Tokens\Token;

class TumblrTest extends OAuthTestCase {

  const BASE_URL = 'https://www.tumblr.com';

  /**
   * @var OAuth
   */
  private $oauth;

  protected function setUp() {
    if ($this->oauth) return;

    $id = getenv('TUMBLR_API_KEY');
    $secret = getenv('TUMBLR_API_SECRET');

    $this->oauth = new OAuth($id, $secret, '1.0');
  }

  public function testCanGetRequestToken() {
    $url = self::BASE_URL . '/oauth/request_token';
    $callback = 'https://oauth.test/flickr/callback';
    $token = $this->oauth->getRequestToken($url, $callback);

    $this->assertInstanceOf(Token::class, $token);
  }

}
