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


class CommunitiesRetrieverRestImpl  implements RegionDataRetriever {

    private $jsonRetriever;
    private $appCache;
    public $cacheTTL;           // set via services.yml property

    public function __construct(JsonRetriever $jsonRetriever, AdapterInterface $appCache) {
        $this->jsonRetriever = $jsonRetriever;
        $this->appCache = $appCache;
    }

    public function getRegions()
    {
        $cachedCommunities = $this->appCache->getItem("communities_array");
        if (!$cachedCommunities->isHit()) {
            $limit = 5000;
            $offset = 0;
            $regions = array();

            while (true) {
                $queryUrl = "/rest/communities?limit=$limit&offset=$offset";
                $result = $this->jsonRetriever->retrieveJsonData($queryUrl);
                $numFound = 0;

                foreach ($result as $item) {
                    $id = $item['id'];
                    $name = $item['name'];
                    $type = TypeFilter::$community;

                    if (trim($name)==true) {
                        $region = new Region($id, $type, $name);
                        array_push($regions, $region);
                        $numFound++;
                    }
                }
                if ($numFound==0 or $numFound<$limit) {
                    break;
                }
                $offset += $limit;
            }

            $cachedCommunities->set($regions);

            if ($this->cacheTTL) {
                $cachedCommunities->expiresAfter(\DateInterval::createFromDateString($this->cacheTTL));
            }

            $this->appCache->save($cachedCommunities);
            return $regions;
        }
        else {
            return $cachedCommunities->get();
        }
    }
} 