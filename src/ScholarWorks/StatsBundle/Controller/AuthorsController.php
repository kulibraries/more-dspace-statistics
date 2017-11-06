<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AuthorsController
 *
 * @package ScholarWorks\StatsBundle\Controller
 *
 * This controller handles ScholarWorks author region forms and inputs.
 */
class AuthorsController extends BaseScholarWorksController
{

    /**
     * This action will provide the basic page data, if no region is specified
     * in the request.  If a region is specified, then the entire SwStats
     * object will be passed to the view.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $statistics = null;
        parent::baseInit($request);

        if ($this->region == null) {
            $authorsPageInitService = $this->get('authorsPageInitializationService');
            $statistics = $authorsPageInitService->getPageData();
        } else {
            $statistics = $this->statisticsService->retrieveStatsForAuthorRegion(
              $this->region,
              $this->fromDate,
              $this->toDate
            );
        }

        return $this->render(
          'ScholarWorksStatsBundle:Authors:index.html.twig',
          [
            'statistics' => $statistics,
            'ga' => $this->getParameter('google_analytics'),
          ]
        );
    }

    public function autocompleteAction(Request $request)
    {
        $searchTerm=$request->query->get('term');
        $authorsPageInitService = $this->get('authorsPageInitializationService');
        /** @var \ScholarWorks\StatsBundle\models\SwStats $statistics */
        $statistics = $authorsPageInitService->getPageData();
        $authors=$statistics->getAuthorRegions();
        if($searchTerm === NULL) {
            throw new Exception();
        }

        $matchingAuthors=[];
        foreach($authors as $author) {
            $authorName=$author->getName();
            if(stristr(trim($authorName), trim($searchTerm))) {
                $authorObj=array(
                  'value' => $author->getDisplayId(),
                  'label' => $authorName
                );
                array_push($matchingAuthors, $authorObj);
            }
        }

        return new JsonResponse($matchingAuthors);

    }

}
