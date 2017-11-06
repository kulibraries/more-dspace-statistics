<?php
/**
 * This interface is used for defining what operations
 * can be performed to get statistics information out of Scholar Works.
 *
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Services;

use ScholarWorks\StatsBundle\models\SwStats;

interface StatisticsService {

    /**
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period and also all available regions, where
     * a region is defined as a collection, community, an author, or the entire repository.
     *
     * @param string $region the string indicating whether the statistics should come from the entire repository,
     *          a single collection, or a single community.
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return SwStats SwStats object containing the request data.
     */
    public function retrieveStatsAndRegions($region, \DateTime $startDate, \DateTime $endDate);

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
     * @return SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForAuthorRegion($region, \DateTime $startDate, \DateTime $endDate);

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
     * @return SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForCommunityOrCollectionRegion($region, \DateTime $startDate, \DateTime $endDate);

    /**
     *
     * This method is used to retrieve a display level (decorated) object containing
     * the statistics for a given time period for the entire repository.    Other region types may not be populated.
     *
     *
     * @param \DateTime $startDate The starting date to use for reporting statistics.
     * @param \DateTime $endDate The ending date to use for reporting statistics.
     * @return SwStats SwStats object containing the request data.
     */
    public function retrieveStatsForEntireRepository(\DateTime $startDate, \DateTime $endDate);
}