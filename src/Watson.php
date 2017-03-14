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
   * The max number of bytes a text can contains in a GET request.
   *
   * @var int
   */
  const MAX_GET_SIZE = 8 * 1024;

  /**
   * Tone filter emotion
   *
   * @var string
   */
  const TONE_FILTER_EMOTION = 'emotion';

  /**
   * Tone filter language
   *
   * @var string
   */
  const TONE_FILTER_LANGUAGE = 'language';

  /**
   * Tone filter social
   *
   * @var string
   */
  const TONE_FILTER_SOCIAL = 'social';

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
   * Should the response contains sentence-level analysis ?
   *
   * @var bool
   */
  private $sentences = true;

  /**
   * To filter by a specific tone
   *
   * @var string
   */
  private $toneFilter = '';

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
   * Take a text, decide if a GET or POST request should be done, make the request
   * and return the response as an array
   * @param string  $text
   * @return array
   */
  public function request ($text) {
    if (strlen($text) < self::MAX_GET_SIZE) {
      $response = $this->get($text);
    } else {
      $response = $this->post($text);
    }
    return $this->parseResponse($response);
  }

  /**
   * Make a GET request and return a Guzzle Response object
   * @param string  $text
   * @return \GuzzleHttp\Psr7\Response
   */
  public function get ($text) {
      $options = $this->getOptions();
      $options['query']['text'] = $text;
      return $this->guzzle->request('GET', '', $options);
  }

  /**
   * Make a POST request and return a Guzzle Response object
   * @param string  $text
   * @return \GuzzleHttp\Psr7\Response
   */
  public function post ($text) {
      $options = $this->getOptions();
      $options['json'] = ['text' => $text];
      return $this->guzzle->request('POST', '', $options);
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
      $this->options['query'] = [
          'version' => $this->getVersion(),
          'sentences' => $this->getSentences()
      ];
      if ($this->getToneFilter()) {
        $this->options['query']['tones'] = $this->getToneFilter();
      }
      return $this->options;
  }

  /**
   * @param string $version
   * @return Beurkinger\MrWatson\Watson
   */
  public function setVersion ($version) {
      $this->version = $version;
      return $this;
  }

  /**
   * @return string
   */
  public function getVersion () {
      return $this->version;
  }

  /**
   * @param bool $bool
   * @return Beurkinger\MrWatson\Watson
   */
  public function setSentences ($bool) {
      $this->sentences = $bool;
      return $this;
  }

  /**
   * @return string
   */
  public function getSentences() {
      return $this->sentences ? 'true' : 'false';
  }

  /**
   * @param string $tone
   * @return Beurkinger\MrWatson\Watson
   */
  public function setToneFilter ($tone) {
      $tones = [self::TONE_FILTER_SOCIAL, self::TONE_FILTER_EMOTION, self::TONE_FILTER_LANGUAGE];
      if (in_array($tone, $tones)) $this->toneFilter = $tone;
      return $this;
  }

  /**
   * @return string
   */
  public function getToneFilter() {
      return $this->toneFilter;
  }

}
