<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/24/14
 * Time: 12:55 PM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\RegionInformationRetriever;
use ScholarWorks\StatsBundle\models\Region;

class CommunityUrlRetrieverRestImpl implements RegionInformationRetriever  {

    private $jsonRetriever;

    public function __construct(JsonRetriever $jsonRetriever) {
        $this->jsonRetriever = $jsonRetriever;
    }

    public function getRegionInformation(Region $region)
    {
        $queryUrl = "/rest/communities/" . $region->getId();
        $result = $this->jsonRetriever->retrieveJsonData($queryUrl);
        $handle = "http://hdl.handle.net/" . $result['handle'];
        return $handle;
    }
} 