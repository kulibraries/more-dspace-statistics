<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/24/14
 * Time: 11:12 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;


class JsonRetriever {

    private $webServiceClient;
    private $webServiceBaseUrl;
    private $logger;

    public function __construct($webServiceBaseUrl, GuzzleHttp\Client $client, LoggerInterface $logger) {
        $this->webServiceBaseUrl = $webServiceBaseUrl;
        $this->webServiceClient = $client; # new Client(['base_uri' => $webServiceBaseUrl]);
        $this->logger = $logger;
    }

    /**
     * @param $url
     * @return mixed|null Json Data on 200 success, null otherwise.
     */
    public function retrieveJsonData($url) {
       $json = null;
        try {
           $response = $this->webServiceClient->get($url, [
               'headers' => ['Content-Type' => 'application/json',
                             'Accept' => 'application/json']
           ]);
           if($response->getStatusCode() == "200") {
               # $json = $response->json();
               $json = json_decode($response->getBody(), true);
           }
        } catch(RequestException $exception) {
            $this->logger->error("Error in making web service call.  Exception:  ", array("cause" => $exception));

            // If we get exceptions this far down into the REST API, we consider it fatal.
            // Throw a generic exception to ScholarWorks\StatsBundle\EventListener\ExceptionListener
            throw new \Exception("Can't connect to DSpace REST API");
        }
        return $json;
    }

    public function getWebServiceClient() {
        return $this->webServiceClient;
    }
} 