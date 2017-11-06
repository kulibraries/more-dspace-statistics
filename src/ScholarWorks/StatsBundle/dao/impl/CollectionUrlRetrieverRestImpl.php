<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/23/15
 * Time: 4:38 PM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\RegionInformationRetriever;
use ScholarWorks\StatsBundle\models\Region;

class CollectionUrlRetrieverRestImpl implements RegionInformationRetriever {

    private $jsonRetriever;

    public function __construct(JsonRetriever $jsonRetriever) {
        $this->jsonRetriever = $jsonRetriever;
    }

    public function getRegionInformation(Region $region)
    {
        $queryUrl = "/rest/collections/" . $region->getId();
        $result = $this->jsonRetriever->retrieveJsonData($queryUrl);
        $handle = "http://hdl.handle.net/" . $result['handle'];
        return $handle;
    }
}
