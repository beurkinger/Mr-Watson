<?php
/**
 * MrWatson, server-side
 *
 * @license MIT
 */
namespace Beurkinger\MrWatson;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

 /**
  * A simple class used to make requests to the IBM Watson tone analyzer API
  *
  * @author Thibault Goehringer <tgoehringer@gmail.com>
  */
class Watson {

  /**
   * The Api endpoint
   *
   * @var string
   */
  const API_ENDPOINT = 'https://gateway.watsonplatform.net/tone-analyzer/api/v3/tone';

  /**
   * The default API version to use
   *
   * @var string
   */
  const DEFAULT_VERSION = '2016-05-19';

  /**
   * The Guzzle client instance
   *
   * @var \GuzzleHttp\Client
   */
  private $guzzle;

  /**
   * The API version to use
   *
   * @var string
   */
  private $version = self::DEFAULT_VERSION;

  /**
   * The request options
   *
   * @var array
   */
  private $options = [
      'exceptions' => false,
      'headers' => [
          'Accept' => 'application/json'
      ]
  ];

  /**
   * Constructor
   */
  function __construct ($username, $password) {
      $this->setGuzzle(new GuzzleClient(['base_uri' => self::API_ENDPOINT]));
      $this->setAuth($username, $password);
  }

  /**
   * Make a request and return an array
   * @param string  $text
   * @return array
   */
  public function request ($text) {
      $options = $this->getOptions();
      $options['query'] = [
          'version' => $this->getVersion(),
          'text' => $text
      ];
      $response = $this->guzzle->request('GET', '', $options);
      return $this->parseResponse($response);
  }

  /**
   * Parse a Guzzle Response object and turn it into an array
   * @param \GuzzleHttp\Psr7\Response $response
   * @return  array
   */
  private function parseResponse(GuzzleResponse $response) {
      $array = [];
      $array['headers'] = $response->getHeaders();
      $array['statusCode'] = $response->getStatusCode();
      $array['content'] = $response->getBody()->getContents();
      return $array;
  }

  /**
   * @param Client  $guzzle
   * @return Beurkinger\MrWatson\Watson
   */
  public function setGuzzle (GuzzleClient $guzzle) {
      $this->guzzle = $guzzle;
      return $this;
  }

  /**
   * @return \GuzzleHttp\Client
   */
  public function getGuzzle () {
      return $this->guzzle;
  }

  /**
   * @param string  $username
   * @param string  $password
   * @return Beurkinger\MrWatson\Watson
   */
  public function setAuth ($username, $password) {
      $options = $this->getOptions();
      $options['auth'] = [$username, $password];
      $this->setOptions($options);
      return $this;
  }

  /**
   * @return array
   */
  public function setOptions (array $options) {
       $this->options = $options;
       return $this;
  }

  /**
   * @return array
   */
  public function getOptions () {
      return $this->options;
  }

  /**
   * @param string $version
   * @return Beurkinger\MrWatson\Watson
   */
  public function setVersion ($version) {
      $this->version = $version;
  }

  /**
   * @return string
   */
  public function getVersion () {
      return $this->version;
  }

}
