<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/19/14
 * Time: 11:36 AM
 */

namespace ScholarWorks\StatsBundle\dao;

use ScholarWorks\StatsBundle\models\Region;

interface StatsDataRetriever {

    public function retrieveRegionData(Region $region, \DateTime $startDate, \DateTime $endDate);

} 