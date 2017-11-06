<?php
/**
 *
 * This interface is used for defining implementations which provide information for only loading index views,
 * which won't contain statistics content for a specific region.
 *
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Services;

use ScholarWorks\StatsBundle\models\SwStats;

interface PageInitializationService {

    /**
     * This method will gather the necessary model information for an index view.  The information gathered
     * is based on the implementation picked.
     *
     * @return SwStats containing information necessary for an index view for a particular region type.
     */
    public function getPageData();

}