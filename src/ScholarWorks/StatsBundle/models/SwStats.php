<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\models;

/**
 * The ScholarWorks Stats (SwStats) Object should contain all of the information necessary for the views.  Since we want
 * to be able to use this for several different kinds of views, where some of the data may not be present, we aren't
 * going to have defensive programming protections in the constructor.
 *
 *
 * Class SwStats
 * @package ScholarWorks\StatsBundle\models
 */
class SwStats {

    private $regionStatisticsForAllTime;
    private $regionStatisticsForTimePeriod;
    private $dateStatisticsStartedToBeGathered;
    private $regionUrl = "";
    private $region;

    private $authorRegions;
    private $communityRegions;
    private $collectionRegions;
    private $entireRepository;

    /**
     * Constructor
     *
     * @param Region $region The region object that is being queried.
     * @param array $entireRepository The region object containing the entire repository.
     * @param array $communityRegions An array containing the community regions.
     * @param array $collectionRegions An array containing the collection regions.
     * @param array $authorRegions An array containing the author regions.
     * @param RegionStatistics $regionStatisticsForAllTime An object containing statistical information about the requested region for all time.
     * @param RegionStatistics $regionStatisticsForTimePeriod An object containing statistical information about the requested region for the given time period.
     * @param \DateTime $dateStatisticsStartedToBeGathered The date at which statistics began to be gathered.
     * @param string $regionUrl The URL pointing back to the region on the scholarworks site.
     * @throws \InvalidArgumentException is thrown if the arguments don't meet the expected requirements.
     *
     */
    public function __construct(
        $region,
        array $entireRepository,
        array $communityRegions,
        array $collectionRegions,
        array $authorRegions,
        $regionStatisticsForAllTime,
        $regionStatisticsForTimePeriod,
        \DateTime $dateStatisticsStartedToBeGathered,
        $regionUrl
    )
    {

        $this->entireRepository = $entireRepository;
        $this->authorRegions = $authorRegions;
        $this->collectionRegions = $collectionRegions;
        $this->communityRegions = $communityRegions;
        $this->regionStatisticsForAllTime = $regionStatisticsForAllTime;
        $this->regionStatisticsForTimePeriod = $regionStatisticsForTimePeriod;
        $this->dateStatisticsStartedToBeGathered = $dateStatisticsStartedToBeGathered;
        $this->regionUrl = $regionUrl;
        $this->region = $region;
    }

    /**
     * getEntireRepositoryRegion()
     *
     * Returns an array containing only the Entire Repository region.  It is returned as an array to make it consistent
     * with the other region getter methods.
     *
     * @return array
     */
    public function getEntireRepositoryRegion() {
        return $this->entireRepository;
    }

    /**
     * getAuthorRegions()
     *
     * Returns an array containing all fo the author regions.
     *
     * @return array
     */
    public function getAuthorRegions() {
        return $this->authorRegions;
    }

    /**
     * getCollectionRegions()
     *
     * Returns an array containing all of the collection regions.
     *
     * @return array
     */
    public function getCollectionRegions() {
        return $this->collectionRegions;
    }

    /**
     * getCommunityRegions()
     *
     * Returns an array containing all of the community regions.
     *
     * @return array
     */
    public function getCommunityRegions() {
        return $this->communityRegions;
    }

    /**
     * getRegionStatisticsForAllTime()
     *
     * Returns the region statistics object for the selected region for the entire time period for which statistics
     * has been captured.
     *
     * @return RegionStatistics
     */
    public function getRegionStatisticsForAllTime() {
        return $this->regionStatisticsForAllTime;
    }

    /**
     * getRegionStatisticsForTimePeriod()
     *
     * Returns the region statistics object for the selected region for the time period selected by the end user.
     *
     * @return RegionStatistics
     */
    public function getRegionStatisticsForTimePeriod() {
        return $this->regionStatisticsForTimePeriod;
    }

    /**
     * Returns the date when statistics started to be gathered in KU ScholarWorks.
     *
     *
     * @return \DateTime
     */
    public function getDateStatisticsStartedToBeGathered() {
        return $this->dateStatisticsStartedToBeGathered;
    }

    /**
     * getRegionUrl()
     *
     * Returns the URL to the selected region.
     *
     * @return string
     */
    public function getRegionUrl() {
        return $this->regionUrl;
    }

    /**
     * getRegion()
     *
     * Returns the selected region.
     *
     * @return Region
     */
    public function getRegion() {
        return $this->region;
    }
}
