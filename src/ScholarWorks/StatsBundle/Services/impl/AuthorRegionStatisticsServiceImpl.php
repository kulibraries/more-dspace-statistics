<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/16/15
 * Time: 10:10 AM
 */

namespace ScholarWorks\StatsBundle\Services\impl;

use ScholarWorks\StatsBundle\dao\ItemSetInformationRetriever;
use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use ScholarWorks\StatsBundle\models\RegionStatistics;
use ScholarWorks\StatsBundle\Services\RegionStatisticsService;
use Psr\Log\LoggerInterface;

class AuthorRegionStatisticsServiceImpl implements RegionStatisticsService {

    private $itemsByAuthorRetriever;
    private $bitstreamInformationRetriever;
    private $totalItemViewsRetriever;
    private $logger;
    private $itemsAddedStartDate;

    public function __construct(
        $dateItemsStartedToBeAdded,
        StatsDataRetriever $itemsByAuthorRetriever,
        ItemSetInformationRetriever $bitstreamInformationRetriever,
        ItemSetInformationRetriever $totalItemViewsRetriever,
        LoggerInterface $logger
    )
    {
        $this->itemsAddedStartDate = \DateTime::createFromFormat('m/d/Y H:i:s',$dateItemsStartedToBeAdded . " 00:00:00");
        $this->logger = $logger;
        $this->itemsByAuthorRetriever = $itemsByAuthorRetriever;
        $this->bitstreamInformationRetriever = $bitstreamInformationRetriever;
        $this->totalItemViewsRetriever = $totalItemViewsRetriever;
    }

/*    public function __construct($dateItemsStartedToBeAdded,
                                StatsDataRetriever $itemsByAuthorRetriever,
                                ItemSetInformationRetriever $bitstreamInformationRetriever,
                                ItemSetInformationRetriever $totalItemViewsRetriever) {

        $this->itemsAddedStartDate = \DateTime::createFromFormat('m/d/Y H:i:s',$dateItemsStartedToBeAdded . " 00:00:00");
        $this->itemsByAuthorRetriever = $itemsByAuthorRetriever;
        $this->bitstreamInformationRetriever = $bitstreamInformationRetriever;
        $this->totalItemViewsRetriever = $totalItemViewsRetriever;
    }*/

    /**
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period for a specific region, where
     * a region is defined as a collection, community, an author, or the entire repository.
     *
     * @param Region $region the string indicating whether the statistics should come from the entire repository,
     *          a single collection, or a single community.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return RegionStatistics a RegionStatistics object containing the requested data.
     */
    public function retrieveStats(Region $region, \DateTime $startDate, \DateTime $endDate) {

        $now = new \DateTime(null);
        $result = "";
        $items = $this->itemsByAuthorRetriever->retrieveRegionData($region, $this->itemsAddedStartDate, $now);

        $this->logger->debug("[AuthorRegionStatisticsServiceImpl:retrieveStats] Items associated with author:  ",
                            array("items" => $items));

        if(count($items) > 0) {
            $bitstreamInformation = $this->bitstreamInformationRetriever->retrieveRegionData($items, $startDate, $endDate);
            $totalItemViews = $this->totalItemViewsRetriever->retrieveRegionData($items, $startDate, $endDate);
            $result = new RegionStatistics($region, $startDate, $endDate,
                $bitstreamInformation['totalBitstreamDownloads'],
                $totalItemViews,
                $bitstreamInformation['topTenDownloads'],
                count($items));
        } else {
            $result = new RegionStatistics($region, $startDate, $endDate,0, 0, array(), 0);
        }

        return $result;
    }
}