<?php

namespace LibAuth\Core;

use Closure;
use Exception as GlobalException;
use GuzzleHttp\Client;
use LibAuth\Exceptions\Exception;

class RequestHandler {

  /**
   * GuzzleHttp Client
   *
   * @var Client
   *
   * @ignore
   */
  private $client;

  /**
   * @ignore
   */
  const CONFIG = [];

  function __construct() {
    $this->client = new Client(self::CONFIG);
  }

  protected function setBaseUri($uri) {
    $config = array_merge(self::CONFIG, [
      'base_uri' => $uri
    ]);

    $this->client = new Client($config);
  }

  public function request($method, $path, $options = []) {
    $method = strtoupper($method);

    if (method_exists($this, 'getDefaultHeaders')) {
      $headers = call_user_func([$this, 'getDefaultHeaders']);
    } else {
      $headers = [];
    }

    if (array_key_exists('headers', $options)) {
      $headers = array_merge($headers, $options['headers']);
    }

    $options['headers'] = $headers;

    $response = $this->client->request($method, $path, $options);

    $code = $response->getStatusCode();
    $body = $response->getBody();
    $contents = $body->getContents();

    if (!($code >= 200 && $code < 300)) {
      throw new Exception($contents, $code);
    }

    return $contents;
  }

  public function get($path, $options = []) {
    return $this->request('GET', $path, $options);
  }

  public function post($path, $options = []) {
    return $this->request('POST', $path, $options);
  }

  public function put($path, $options = []) {
    return $this->request('PUT', $path, $options);
  }

  public function patch($path, $options = []) {
    return $this->request('PATCH', $path, $options);
  }

  public function delete($path, $options = []) {
    return $this->request('DELETE', $path, $options);
  }

}
