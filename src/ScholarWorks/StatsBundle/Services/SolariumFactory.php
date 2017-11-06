<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 10/25/17
 * Time: 3:08 PM
 */

namespace ScholarWorks\StatsBundle\Services;

use Solarium\Client;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SolariumFactory
{

    static $dataCollector=null;

    static function getSolrCore($dsn)
    {

        $parsed_dsn = parse_url($dsn);
        if(!isset($parsed_dsn['port'])) {
          if($parsed_dsn['scheme']=='http') $parsed_dsn['port']=80;
          if($parsed_dsn['scheme']=='https') $parsed_dsn['port']=443;
        }
        $config = [
          'endpoint' => [
            'localhost' => $parsed_dsn,
          ],
        ];
        $client = new Client($config);
        // Only in debug...
        if (isset(self::$dataCollector)) {
            $client->registerPlugin('symfonylogger', self::$dataCollector);
        }
        return $client;
    }

    public function setLogger($dataCollector, $debug) {
      if($debug) {
        self::$dataCollector=$dataCollector;
      }
    }

}