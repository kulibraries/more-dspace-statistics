<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/26/15
 * Time: 11:57 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\RegionInformationRetriever;
use ScholarWorks\StatsBundle\models\Region;

class DefaultUrlRetrieverImpl implements RegionInformationRetriever {

    public $webServiceBaseUrl = null;

    public function getRegionInformation(Region $region)
    {
        return $this->webServiceBaseUrl;
    }
}