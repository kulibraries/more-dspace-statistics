<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/26/15
 * Time: 10:59 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl\authors;

use ScholarWorks\StatsBundle\dao\RegionInformationRetriever;
use ScholarWorks\StatsBundle\models\Region;

class AuthorUrlRetrieverImpl implements RegionInformationRetriever {

    public $webServiceBaseUrl = null;

    public function getRegionInformation(Region $region)
    {
        $author = $region->getName();
        $fullUrl = $this->webServiceBaseUrl . "/discover?filtertype=author&filter_relational_operator=equals&filter=" . $author;
        return $fullUrl;
    }

}