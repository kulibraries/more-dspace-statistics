<?php


namespace ScholarWorks\StatsBundle\Services\impl;

use ScholarWorks\StatsBundle\Services\RegionStatisticsService;
use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\dao\RegionDataRetriever;
use ScholarWorks\StatsBundle\models\SwStats;
use ScholarWorks\StatsBundle\models\RegionStatistics;
use ScholarWorks\StatsBundle\models\Region;

class CommunityAndCollectionStatsServiceImpl implements RegionStatisticsService {

    private $topTenBitStreamStatsRetriever;
    private $totalBitStreamDownloadsRetriever;
    private $totalItemViewsRetriever;
    private $numberOfItemsInRegionRetriever;
    private $itemsAddedStartDate;

    public function __construct(
        $dateItemsStartedToBeAdded,
        StatsDataRetriever $topTenBitStreamStatsRetriever,
        StatsDataRetriever $totalBitStreamDownloadsRetriever,
        StatsDataRetriever $totalItemViewsRetriever,
        StatsDataRetriever $numberOfItemsInRegionRetriever)
    {
        $this->itemsAddedStartDate = \DateTime::createFromFormat('m/d/Y H:i:s',$dateItemsStartedToBeAdded . " 00:00:00");
        $this->topTenBitStreamStatsRetriever = $topTenBitStreamStatsRetriever;
        $this->totalBitStreamDownloadsRetriever = $totalBitStreamDownloadsRetriever;
        $this->totalItemViewsRetriever = $totalItemViewsRetriever;
        $this->numberOfItemsInRegionRetriever = $numberOfItemsInRegionRetriever;
    }


    /**
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period and also all available regions, where
     * a region is defined as a collection, community, or the entire repository.
     *
     * @param Region $region the string indicating whether the statistics should come from the entire repository,
     *          a single collection, or a single community.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return RegionStatistics RStats object containing the request data.
     */
    public function retrieveStats(Region $region, \DateTime $startDate, \DateTime $endDate)
    {
        $totalBitStreamDownloads = $this->totalBitStreamDownloadsRetriever->retrieveRegionData($region,$startDate, $endDate);
        $topTenDownloadsRequestedTimePeriod = $this->topTenBitStreamStatsRetriever->retrieveRegionData($region,$startDate, $endDate);
	    $totalItemViews = $this->totalItemViewsRetriever->retrieveRegionData($region,$startDate, $endDate);
        $totalNumberOfItemsInRegionUpToTimePeriod = $this->numberOfItemsInRegionRetriever->retrieveRegionData($region,$this->itemsAddedStartDate, $endDate);

        $regionStatisticsForTimePeriod =  new RegionStatistics($region,
            $startDate,
            $endDate,
            $totalBitStreamDownloads,
            $totalItemViews,
            $topTenDownloadsRequestedTimePeriod,
            $totalNumberOfItemsInRegionUpToTimePeriod);

        return $regionStatisticsForTimePeriod;
    }
}