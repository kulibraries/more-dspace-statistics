<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class CommunitiesAndCollectionsController
 * @package ScholarWorks\StatsBundle\Controller
 *
 * This controller is meant for handling all of the Community and Collection regions forms and inputs.
 */

class CommunitiesAndCollectionsController extends BaseScholarWorksController
{
    /**
     * indexAction()
     *
     * This action will provide the basic page data, if no region is specified in the request.
     * If a region is specified, then the entire SwStats object will be passed to the view.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $statistics = null;
        parent::baseInit($request);

        if ($this->region == null) {
            $pageInitService = $this->get('commAndCollPageInitializationService');
            $statistics = $pageInitService->getPageData();
        } else {
            $statistics = $this->statisticsService->retrieveStatsForCommunityOrCollectionRegion(
                $this->region,
                $this->fromDate,
                $this->toDate
            );
        }

        return $this->render(
            'ScholarWorksStatsBundle:CommunitiesAndCollections:index.html.twig',
            array(
                'statistics' => $statistics,
                'ga' => $this->getParameter('google_analytics')
            )
        );
    }

    public function autocompleteAction(Request $request)
    {
        $searchTerm = $request->query->get('term');

        if ($searchTerm === NULL) {
            throw new Exception();
        }

        $ccPageInitService = $this->get('commAndCollPageInitializationService');

        /** @var \ScholarWorks\StatsBundle\models\SwStats $statistics */
        $statistics = $ccPageInitService->getPageData();

        $collections = $statistics->getCollectionRegions();
        $communities = $statistics->getCommunityRegions();

        $matchingCommsAndColls = [];

        foreach ($collections as $collection) {
            $collectionName = $collection->getName();
            if (stristr(trim($collectionName), trim($searchTerm))) {
                $tmpObj = array(
                    'value' => $collection->getDisplayId(),
                    'label' => $collection->getDisplayName()
                );
                array_push($matchingCommsAndColls, $tmpObj);
            }
        }

        foreach ($communities as $community) {
            $communityName = $community->getName();
            if (stristr(trim($communityName), trim($searchTerm))) {
                $tmpObj = array(
                    'value' => $community->getDisplayId(),
                    'label' => $community->getDisplayName()
                );
                array_push($matchingCommsAndColls, $tmpObj);
            }
        }
        return new JsonResponse($matchingCommsAndColls);
    }
}
