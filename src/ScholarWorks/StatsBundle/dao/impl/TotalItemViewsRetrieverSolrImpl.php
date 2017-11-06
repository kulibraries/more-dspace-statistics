<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/24/14
 * Time: 11:10 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use Solarium;


class TotalItemViewsRetrieverSolrImpl implements StatsDataRetriever {

    private $solrClient;

    public function __construct(Solarium\Client $solrClient) {
        $this->solrClient = $solrClient;
    }

    public function retrieveRegionData(Region $region, \DateTime $startDate, \DateTime $endDate)
    {
        $tf = new TypeFilter();
        $solrClient = $this->solrClient;
        $typeQueryPortion = $tf->getStatisticsSolrFilter($region->getType(), $region->getId());
        $timeQueryPortion = "time:[" . $startDate->format('Y-m-d\TH:i:s\Z') . " TO " . $endDate->format('Y-m-d\TH:i:s\Z') . "]";

        //  prepare solr query...
        $solrQuery = $solrClient->createSelect();
        $solrQuery->setQuery("*");
        $solrQuery->setStart(0);
        $solrQuery->setRows(0);
        $solrQuery->setFields(array("*", "score"));
        $solrQuery->createFilterQuery('notBot')->setQuery("NOT(isBot:true) " . $typeQueryPortion);
        $solrQuery->createFilterQuery('timeQuery')->setQuery($timeQueryPortion);
        $solrQuery->createFilterQuery('type')->setQuery("type:2");
        $solrQuery->createFilterQuery('statistics_type')->setQuery("statistics_type:view");

        // execute solr query...
        $solrResult = null;
        try {
            $solrResult = $solrClient->execute($solrQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }

        return $solrResult->getData()['response']['numFound'];
    }
}
