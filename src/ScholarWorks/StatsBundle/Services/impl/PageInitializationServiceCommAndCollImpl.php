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
 * This class is used for gathering dspace community and collection information for loading an index view,
 * but won't contain statistics content for a specific region.
 *
 * Class PageInitializationServiceCommAndCollImpl
 * @package ScholarWorks\StatsBundle\Services\impl
 */
class PageInitializationServiceCommAndCollImpl implements PageInitializationService {

    private $dateStatsGathered;
    private $collectionsRetriever;
    private $communitiesRetriever;
    public $regionUrl;     /* This var must be set via 'properties' in Resources/Config/services.yml. */

    /**
     * Constructor
     *
     * @param RegionDataRetriever $collectionsRetriever
     * @param RegionDataRetriever $communitiesRetriever
     * @param DateService         $dateService
     */
    public function __construct(
            RegionDataRetriever $collectionsRetriever,
            RegionDataRetriever $communitiesRetriever,
            DateService $dateService)
    {
        $this->dateStatsGathered = $dateService->getDateStatsStart();
        $this->collectionsRetriever = $collectionsRetriever;
        $this->communitiesRetriever = $communitiesRetriever;
    }

    /**
     * This method will gather the necessary model information for a community / collection index view.
     *
     * @return SwStats containing information for communities, collections, and the date statistics started to be gathered.
     */
    public function getPageData() {
        return new SwStats(null, array(), $this->communitiesRetriever->getRegions(), $this->collectionsRetriever->getRegions(), array(), null, null, $this->dateStatsGathered, $this->regionUrl);
    }

}