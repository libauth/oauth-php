<?php

namespace LibAuth\Versions;

use LibAuth\Core\OAuthCore;
use LibAuth\Tokens\AccessToken;
use LibAuth\Tokens\RequestToken;

class OAuth_1_0 extends OAuthCore {

  private function createSortedParams(array $params) {
    $result = [];

    foreach ($params as $name => $value) {
      $result[] = rawurlencode($name) . "=" . rawurlencode($value);
    }

    sort($result, SORT_ASC);

    return $result;
  }

  /**
   * Sign OAuth Request
   *
   * @param array $params
   * @param string $method
   * @param string $url
   * @return void
   */
  final protected function signOAuthRequest(array &$params, $method, $url, $secret = NULL) {
    $timestamp = time();
    $nonce = hash('sha1', "{$timestamp}|".mt_rand(10000, 90000));

    $params['oauth_nonce'] = $nonce;
    $params['oauth_signature_method'] = 'HMAC-SHA1';
    $params['oauth_timestamp'] = $timestamp;
    $params['oauth_version'] = '1.0';

    $sortedParams = $this->createSortedParams($params);
    $paramString = implode('&', $sortedParams);

    $baseParams = [
      strtoupper($method),
      rawurlencode($url),
      rawurlencode($paramString)
    ];

    $baseString = implode('&', $baseParams);

    if (method_exists($this, 'getSigningKey')) {
      $signingKey = call_user_func([$this, 'getSigningKey']);
    } else {
      $signingKey = rawurldecode($this->secret) . '&';
      if ($secret) $signingKey .= rawurlencode($secret);
    }

    $signature = hash_hmac('sha1', $baseString, $signingKey, true);
    $signature = base64_encode($signature);

    $params['oauth_signature'] = $signature;
  }

  /**
   * Get Request Token
   *
   * @return RequestToken
   */
  public function getRequestToken($url, $callback = NULL) {
    $params = [
      'oauth_consumer_key' => $this->id
    ];

    if (!empty($callback)) {
      $params['oauth_callback'] = $callback;
    }

    $this->signOAuthRequest($params, 'POST', $url);

    $resp = $this->handler->post($url, [
      'form_params' => $params
    ]);

    parse_str($resp, $data);

    return new RequestToken($data);
  }

  public function getAccessToken($url, RequestToken $token, $verifier) {
    $params = [
      'oauth_consumer_key' => $this->id,
      'oauth_token' => $token->oauth_token,
      'oauth_verifier' => $verifier
    ];

    $this->signOAuthRequest($params, 'POST', $url, $token->oauth_token_secret);

    $resp = $this->handler->post($url, [
      'form_params' => $params
    ]);

    parse_str($resp, $data);

    return new AccessToken($data);
  }

}

