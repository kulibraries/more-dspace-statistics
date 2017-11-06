<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Services\impl;

use ScholarWorks\StatsBundle\models\SwStats;
use ScholarWorks\StatsBundle\dao\RegionDataRetriever;
use ScholarWorks\StatsBundle\Services\PageInitializationService;


/**
 * This class is used for gathering dspace author information for loading an index view,
 * but won't contain statistics content for a specific region.
 *
 * Class PageInitializationServiceAuthorImpl
 * @package ScholarWorks\StatsBundle\Services\impl
 */
class PageInitializationServiceAuthorImpl implements PageInitializationService {

    private $dateStatsGathered;
    private $authorsRetriever;
    public $regionUrl;         /* This var must be set via 'properties' in Resources/Config/services.yml. */

    /**
     * Constructor
     *
     * @param RegionDataRetriever $authorsRetriever
     * @param DateService $dateService
     */
    public function __construct(RegionDataRetriever $authorsRetriever, DateService $dateService)
    {
        $this->dateStatsGathered = $dateService->getDateStatsStart();
        $this->authorsRetriever = $authorsRetriever;
    }

    /**
     * This method will gather the necessary model information for a author index view.
     *
     * @return SwStats containing information for authors and the date statistics started to be gathered.
     */
    public function getPageData() {
        return new SwStats(null,array(), array(),array(), $this->authorsRetriever->getRegions(),null, null, $this->dateStatsGathered, $this->regionUrl);
    }
}