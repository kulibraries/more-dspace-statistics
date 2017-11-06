<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/24/14
 * Time: 12:56 PM
 */

namespace ScholarWorks\StatsBundle\dao;


interface RegionDataRetriever {

    /**
     * @return array of Regions
     */
    public function getRegions();
} 