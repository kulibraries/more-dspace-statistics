<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/2/15
 * Time: 9:48 AM
 */

namespace ScholarWorks\StatsBundle\Services\impl;

use ScholarWorks\StatsBundle\Services\RegionStatisticsService;
use ScholarWorks\StatsBundle\dao\impl\TypeFilter;
use ScholarWorks\StatsBundle\Services\TypeFactory;

class TypeService implements TypeFactory
{

    private $typeFilter;
    private $communityAndCollectionRegionStatisticsService;
    private $authorRegionStatisticsService;

    public function __construct(TypeFilter $typeFilter,
                                RegionStatisticsService $communityAndCollectionRegionStatisticsService,
                                RegionStatisticsService $authorRegionStatisticsService) {

        $this->typeFilter = $typeFilter;
        $this->communityAndCollectionRegionStatisticsService = $communityAndCollectionRegionStatisticsService;
        $this->authorRegionStatisticsService = $authorRegionStatisticsService;
    }

    public function getRegionStatisticsService($type) {
        $result = "";

        if($type == TypeFilter::$collection || $type == TypeFilter::$community) {
            $result = $this->communityAndCollectionRegionStatisticsService;
        } else if($type == TypeFilter::$author) {
            $result = $this->authorRegionStatisticsService;
        }
        else {
            $result = $this->communityAndCollectionRegionStatisticsService;
        }

        return $result;
    }
}