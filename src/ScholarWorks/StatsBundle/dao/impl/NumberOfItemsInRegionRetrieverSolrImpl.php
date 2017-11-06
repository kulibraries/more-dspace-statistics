<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 10/8/14
 * Time: 10:30 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use Solarium;

class NumberOfItemsInRegionRetrieverSolrImpl implements StatsDataRetriever {

    private $solrClient;

    public function __construct(Solarium\Client $solrClient) {
        $this->solrClient = $solrClient;
    }

    public function retrieveRegionData(Region $region, \DateTime $startDate, \DateTime $endDate) {
        $tf = new TypeFilter();

        $solrQuery = $this->solrClient->createSelect();
        $solrQuery->setQuery("dc.date.accessioned_dt:[" . $startDate->format('Y-m-d\TH:i:s\Z') . " TO " . $endDate->format('Y-m-d\TH:i:s\Z') . "]");
        $solrQuery->createFilterQuery('fq')->setQuery(
            "search.resourcetype:2 AND NOT withdrawn:true " .
            $tf->getSearchSolrFilter($region->getType(), $region->getId())
        );
        $solrQuery->setStart(0);
        $solrQuery->setRows(0);
        $solrResult = null;
        try {
            $solrResult = $this->solrClient->execute($solrQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }
        $json = $solrResult->getData();
        $totalNumberOfItemsInRegion = $json['response']['numFound'];
        return $totalNumberOfItemsInRegion;
    }
}