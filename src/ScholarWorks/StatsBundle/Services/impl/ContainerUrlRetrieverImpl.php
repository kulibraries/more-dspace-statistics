<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/23/15
 * Time: 4:49 PM
 */

namespace ScholarWorks\StatsBundle\Services\impl;


use ScholarWorks\StatsBundle\dao\impl\TypeFilter;
use ScholarWorks\StatsBundle\dao\RegionInformationRetriever;
use ScholarWorks\StatsBundle\Services\TypeFactory;

class ContainerUrlRetrieverImpl implements TypeFactory{

    private $authorUrlRetriever;
    private $collectionUrlRetriever;
    private $communityUrlRetriever;
    private $defaultUrlRetriever;
    private $typeFilter;

    public function __construct(TypeFilter $typeFilter,
                                RegionInformationRetriever $authorUrlRetriever,
                                RegionInformationRetriever $collectionUrlRetriever,
                                RegionInformationRetriever $communityUrlRetriever,
                                RegionInformationRetriever $defaultUrlRetriever) {
        $this->typeFilter = $typeFilter;
        $this->authorUrlRetriever = $authorUrlRetriever;
        $this->collectionUrlRetriever = $collectionUrlRetriever;
        $this->communityUrlRetriever = $communityUrlRetriever;
        $this->defaultUrlRetriever = $defaultUrlRetriever;
    }


    public function getRegionStatisticsService($type) {
        $result = "";

        if($type == TypeFilter::$collection ) {
            $result = $this->collectionUrlRetriever;
        } else if ($type == TypeFilter::$community) {
            $result = $this->communityUrlRetriever;
        } else if($type == TypeFilter::$author) {
            $result = $this->authorUrlRetriever;
        } else {
            $result = $this->defaultUrlRetriever;
        }

        return $result;
    }
}