<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/24/14
 * Time: 12:55 PM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\RegionDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use Symfony\Component\Cache\Adapter\AdapterInterface;


class CollectionsRetrieverRestImpl implements RegionDataRetriever {

    private $jsonRetriever;
    private $appCache;
    public $cacheTTL;           // set via services.yml property

    public function __construct(JsonRetriever $jsonRetriever, AdapterInterface $appCache) {
        $this->jsonRetriever = $jsonRetriever;
        $this->appCache = $appCache;
    }

    public function getRegions()
    {
        $cachedRegions = $this->appCache->getItem('collection_array');

        if (!$cachedRegions->isHit()) {
            $limit = 5000;
            $offset = 0;
            $regions = array();

            while (true) {
                $queryUrl = "/rest/collections?limit=$limit&offset=$offset";
                $result = $this->jsonRetriever->retrieveJsonData($queryUrl);

                $numFound = 0;
                foreach ($result as $item) {
                    $id = $item['id'];
                    $name = $item['name'];
                    $type = TypeFilter::$collection;

                    if (trim($name) == true) {
                        $region = new Region($id, $type, $name);
                        $numFound++;
                        array_push($regions, $region);
                    }
                }
                if ($numFound==0 or $numFound<$limit) {
                    break;
                }
                $offset += $limit;
            }

            $cachedRegions->set($regions);

            if ($this->cacheTTL) {
                $cachedRegions->expiresAfter(\DateInterval::createFromDateString($this->cacheTTL));
            }

            $this->appCache->save($cachedRegions);
            return $regions;
        }
        else {
            return $cachedRegions->get();
        }
    }
}