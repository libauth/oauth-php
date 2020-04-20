<?php

namespace LibAuth\Core;

use Closure;
use Exception as GlobalException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LibAuth\Exceptions\Exception;
use ReflectionException;
use ReflectionObject;

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
   * Reflection Object
   *
   * @var ReflectionObject
   */
  private $ref;

  /**
   * @ignore
   */
  const REQUEST_CONFIG = [
    'headers' => [
      'User-Agent' => 'LibAuth/OAuth Library'
    ]
  ];

  /**
   * Request Handler Constructor
   *
   * @param string $endpoint
   */
  function __construct($endpoint, $sslVerify = true) {
    $this->ref = new ReflectionObject($this);

    $stack = new HandlerStack();
    $stack->setHandler(new CurlHandler());

    $callable = Closure::fromCallable([$this, 'mapRequest']);
    $stack->push(Middleware::mapRequest($callable->bindTo($this)));

    $callable = Closure::fromCallable([$this, 'mapResponse']);
    $stack->push(Middleware::mapResponse($callable->bindTo($this)));

    if (substr($endpoint, 0, -1) !== '/') {
      $endpoint = "{$endpoint}/";
    }

    $config = array_merge(static::REQUEST_CONFIG, [
      'verify' => $sslVerify,
      'base_uri' => $endpoint,
      'handler' => $stack
    ]);

    $this->client = new Client($config);
  }

  final protected function mapRequest(Request $request) {
    $this->call('onBeforeRequest', [&$request]);
    $this->call('authenticate', [&$request]);

    return $request;
  }

  final protected function mapResponse(Response $response) {
    $this->call('onAfterRequest', [&$response]);
    return $response;
  }

  final protected function &reference() {
    return $this->ref;
  }

  final protected function call($name, array $args = []) {
    if (!$this->ref->hasMethod($name)) return false;

    $method = $this->ref->getMethod($name);

    if (!$method->isProtected()) {
      throw new Exception("Access level for '{$this->ref->name}::{$name}' must be protected!");
    }

    $method->setAccessible(true);

    return $method->invokeArgs($this, $args);
  }

  public function request($method, $path, $options = []) {
    $method = strtoupper($method);

    if (substr($path, 0, 1) === '/') {
      $path = substr($path, 1);
    }

    $headers = $this->call('getDefaultHeaders', [$method, $path, &$options]);
    if (!$headers) $headers = [];

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
