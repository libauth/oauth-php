<?php

use LibAuth\OAuth;
use LibAuth\Tokens\Token;

class FlickrTest extends OAuthTestCase {

  const BASE_URL = 'https://www.flickr.com/services';

  /**
   * @var OAuth
   */
  private $oauth;

  protected function setUp() {
    if ($this->oauth) return;

    $id = getenv('FLICKR_API_KEY');
    $secret = getenv('FLICKR_API_SECRET');

    $this->oauth = new OAuth($id, $secret, self::BASE_URL, '1.0');
  }

  public function testCanGetRequestToken() {
    $callback = 'https://oauth.test/flickr/callback';

    $token = $this->oauth->getRequestToken([
      'url' => '/oauth/request_token',
      'callback_url' => $callback
    ]);

    $this->assertInstanceOf(Token::class, $token);
  }

}
