<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Services\impl;

use ScholarWorks\StatsBundle\Services\StatisticsService;
use ScholarWorks\StatsBundle\dao\RegionDataRetriever;
use ScholarWorks\StatsBundle\models\SwStats;
use ScholarWorks\StatsBundle\models\Region;
use ScholarWorks\StatsBundle\Services\TypeFactory;


class BasicStatisticsServiceImpl implements StatisticsService {

    private $dateStatisticsStartedToBeGathered;
    private $collectionsRetriever;
    private $communitiesRetriever;
    private $authorsRetriever;
    private $typeService;
    private $containerUrlRetriever;

    /**
     * Constructor
     *
     * @param TypeFactory $typeService
     * @param TypeFactory $containerUrlRetriever
     * @param RegionDataRetriever $collectionsRetriever
     * @param RegionDataRetriever $communitiesRetriever
     * @param RegionDataRetriever $authorsRetriever
     * @param DateService $dateService
     */

    public function __construct(
        TypeFactory $typeService,
        TypeFactory $containerUrlRetriever,
        RegionDataRetriever $collectionsRetriever,
        RegionDataRetriever $communitiesRetriever,
        RegionDataRetriever $authorsRetriever,
        DateService $dateService)
    {
        $this->dateStatisticsStartedToBeGathered = $dateService->getDateStatsStart();
        $this->typeService = $typeService;
        $this->collectionsRetriever = $collectionsRetriever;
        $this->communitiesRetriever = $communitiesRetriever;
        $this->authorsRetriever = $authorsRetriever;
        $this->containerUrlRetriever = $containerUrlRetriever;
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
     * @return object SwStats object containing the request data.
     */
    private function retrieveStatsForRegion(Region $region, array $authorRegions, array $communityRegions, array $collectionRegions, array $entireRepository,
                                           \DateTime $startDate, \DateTime $endDate) {
        $now = new \DateTime(null);
	    $now->setTime(23,59,59);
	
        $urlRetriever = $this->containerUrlRetriever->getRegionStatisticsService($region->getType());
        $swUrl = $urlRetriever->getRegionInformation($region);


        $regionStatisticsService = $this->typeService->getRegionStatisticsService($region->getType());
        $regionStatisticsForAllTime = $regionStatisticsService->retrieveStats($region, $this->dateStatisticsStartedToBeGathered, $now);
        $regionStatisticsForTimePeriod = $regionStatisticsService->retrieveStats($region, $startDate, $endDate);
	
        $statistics = new SwStats($region, $entireRepository, $communityRegions, $collectionRegions, $authorRegions,
                                  $regionStatisticsForAllTime, $regionStatisticsForTimePeriod, $this->dateStatisticsStartedToBeGathered, $swUrl);

        return $statistics;
    }

    /**
     *
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period for the specified author region.  It will also contain information
     * all available author regions.  Other region types may not be populated.
     *
     *
     * @param string $region the string indicating which author region the statistic information should be gathered from.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return object SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForAuthorRegion($region,  \DateTime $startDate, \DateTime $endDate) {

        $authorRegions = $this->authorsRetriever->getRegions();

        $regionObject = array_values(
            array_filter(
                $authorRegions,
                function(Region $testRegion) use ($region)
                {
                    return ($region === $testRegion->getDisplayId());
                }
            )
        );

        return $this->retrieveStatsForRegion(
            $regionObject[0],
            $authorRegions,
            array(),
            array(),
            array(),
            $startDate,
            $endDate
        );
    }

    /**
     *
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period for the specified community or collection region.
     * It will also contain information all available author regions.  Other region types may not be populated.
     *
     *
     * @param string $region the string indicating which community or collection region the
     *                       statistic information should be gathered from.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return object SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForCommunityOrCollectionRegion($region,  \DateTime $startDate, \DateTime $endDate) {
        $communityRegions = $this->communitiesRetriever->getRegions();
        $collectionRegions = $this->collectionsRetriever->getRegions();

        $regions = array();
        $regions = array_merge($regions, $communityRegions);
        $regions = array_merge($regions, $collectionRegions);

        $regionObject = array_values(array_filter($regions, function(Region $testRegion) use ($region) {
            return ($region === $testRegion->getDisplayId());
        }));

        return $this->retrieveStatsForRegion($regionObject[0], array(), $communityRegions, $collectionRegions, array(), $startDate, $endDate);
    }

    /**
     *
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period for the entire repository.    Other region types may not be populated.
     *
     *
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return object SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForEntireRepository(\DateTime $startDate, \DateTime $endDate) {
        $entireRepository = new Region("1", "Repository", "Entire Repository");

        return $this->retrieveStatsForRegion($entireRepository, array(), array(), array(), array($entireRepository), $startDate, $endDate);
    }

    /**
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period and also all available regions, where
     * a region is defined as a collection, community, an author, or the entire repository.
     *
     * @param string $region the string indicating whether the statistics should come from the entire repository,
     *          a single collection, or a single community.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return object SwStats SwStats object containing the request data.
     */
    public function retrieveStatsAndRegions($region, \DateTime $startDate, \DateTime $endDate)
    {
        $entireRepository = new Region("1", "Repository", "Entire Repository");
        $regions = array($entireRepository);
        $communityRegions = $this->communitiesRetriever->getRegions();
        $collectionRegions = $this->collectionsRetriever->getRegions();
        $authorRegions = $this->authorsRetriever->getRegions();
        $regions = array_merge($regions, $communityRegions);
        $regions = array_merge($regions, $collectionRegions);
        $regions = array_merge($regions, $authorRegions);

        $regionObject = array_values(array_filter($regions, function(Region $testRegion) use ($region) {
            return ($region === $testRegion->getDisplayId());
        }));

        return $this->retrieveStatsForRegion($regionObject[0],$authorRegions,$communityRegions, $collectionRegions, array($entireRepository), $startDate, $endDate);
    }

}