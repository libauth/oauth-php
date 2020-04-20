<?php

namespace LibAuth\Versions;

use GuzzleHttp\Psr7\Request;
use LibAuth\Core\OAuthCore;
use LibAuth\Exceptions\Exception;
use LibAuth\Params\AccessTokenParams;
use LibAuth\Params\RequestTokenParams;
use LibAuth\Tokens\AccessToken;
use LibAuth\Tokens\RequestToken;

use function GuzzleHttp\Psr7\stream_for;

class OAuth_1_0 extends OAuthCore {

  /**
   * Create Sorted Parameters
   *
   * This is used by the `generateSignature` method to sort the request parameters.
   * The values of the sorted parameters are `rawurlencode`d.
   *
   * @param array $params
   * @return string[]
   *
   * @ignore
   */
  private function createSortedParams(array $params) {
    $result = [];

    foreach ($params as $name => $value) {
      $result[] = rawurlencode($name) . "=" . rawurlencode($value);
    }

    sort($result, SORT_ASC);

    return $result;
  }

  /**
   * Generate OAuth Signature
   *
   * @param string $method
   * @param string $url
   * @param array $params
   * @return string|boolean
   */
  protected function generateSignature($method, $url, array &$params = []) {
    $token = $this->getToken();

    $timestamp = time();
    $nonce = hash('sha1', "{$timestamp}|".mt_rand(10000, 90000));

    if ($token && !array_key_exists('oauth_token', $params)) {
      $params['oauth_token'] = $token->oauth_token;
    }

    $params['oauth_consumer_key'] = $this->id;
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

    $signingKey = rawurldecode($this->secret) . '&';
    if ($token && $token->oauth_token_secret) {
      $signingKey .= rawurlencode($token->oauth_token_secret);
    }

    $signature = hash_hmac('sha1', $baseString, $signingKey, true);
    $signature = base64_encode($signature);

    return $signature;
  }

  protected function authenticate(Request &$request) {
    $method = strtoupper($request->getMethod());

    $uri = $request->getUri();
    list($url) = explode('?', (string)$uri);

    $authType = $this->getAuthType();

    switch ($authType) {
      case self::AUTH_TYPE_FORM: {
        $body = $request->getBody();
        $content = $body->getContents();
        parse_str($content, $params);

        $signature = $this->generateSignature($method, $url, $params);
        $params['oauth_signature'] = $signature;

        $body = stream_for(http_build_query($params));
        $request = $request->withBody($body);
      } break;

      case self::AUTH_TYPE_URI: {
        $content = $uri->getQuery();
        parse_str($content, $params);

        $signature = $this->generateSignature($method, $url, $params);
        $params['oauth_signature'] = $signature;

        $uri = $uri->withQuery(http_build_query($params));
        $request = $request->withUri($uri);
      } break;
    }
  }

  /**
   * Handle Get Request Token
   *
   * @param RequestTokenParams $params
   *
   * @return RequestToken
   */
  protected function handleGetRequestToken(RequestTokenParams $params) {
    $input = [];

    if (isset($params->callback_url)) {
      $input['oauth_callback'] = $params->callback_url;
    }

    $this->setAuthType(self::AUTH_TYPE_FORM);

    $resp = $this->post($params->url, [
      'form_params' => $input
    ]);

    parse_str($resp, $data);

    return new RequestToken($data);
  }

  /**
   * Handle Get Access Token
   *
   * @param AccessTokenParams $params
   *
   * @return AccessToken
   */
  protected function handleGetAccessToken(AccessTokenParams $params) {
    $token = $this->getToken();

    $input = [
      'oauth_token' => $token->oauth_token,
      'oauth_verifier' => $params->verifier
    ];

    $this->setAuthType(self::AUTH_TYPE_FORM);

    $resp = $this->post($params->url, [
      'form_params' => $input
    ]);

    parse_str($resp, $data);

    return new AccessToken($data);
  }

}

