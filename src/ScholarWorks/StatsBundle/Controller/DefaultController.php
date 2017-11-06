<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 *
 */

namespace ScholarWorks\StatsBundle\Controller;

use ScholarWorks\StatsBundle\models\Region;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class DefaultController
 * @package ScholarWorks\StatsBundle\Controller
 *
 * This controller is the catch all controller, which can handle any type of region.  If no region is specified, it
 * will provide the entire repository statistics.
 */

class DefaultController extends BaseScholarWorksController
{

    /**
     * indexAction()
     *
     * This method will handle all the regions as inputs.  If no region is specified in the request, it will gather the
     * stats for the entire repository.  If a region is specified, it will gather the stats for the specified
     * repository.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $statistics = null;
        $this->baseInit($request);

        if ($this->region == null) {
            $entireRepository = new Region("1", "Repository", "Entire Repository");
            $this->region = $entireRepository->getDisplayId();

            $statistics = $this->statisticsService->retrieveStatsForEntireRepository(
                $this->fromDate,
                $this->toDate
            );

        } else {
            $statistics = $this->statisticsService->retrieveStatsAndRegions(
                $this->region,
                $this->fromDate,
                $this->toDate
            );
        }

        return $this->render(
            'ScholarWorksStatsBundle:Default:index.html.twig',
            array(
                'statistics' => $statistics,
                'ga' => $this->getParameter('google_analytics')
            )
        );
    }
}
