<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/26/15
 * Time: 11:44 AM
 */

namespace ScholarWorks\StatsBundle\dao;

use ScholarWorks\StatsBundle\models\Region;

interface RegionInformationRetriever {

    public function getRegionInformation(Region $region);

}