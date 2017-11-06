<?php
/**
 * This interface is used for defining what operations
 * can be performed to get statistics information out of Scholar Works.
 *
 * @author Matthew Copeland
 */

namespace ScholarWorks\StatsBundle\Services;

use ScholarWorks\StatsBundle\models\RegionStatistics;
use ScholarWorks\StatsBundle\models\Region;

interface RegionStatisticsService {

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
    public function retrieveStats(Region $region, \DateTime $startDate, \DateTime $endDate);
}