<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/20/15
 * Time: 11:43 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl\authors;

use ScholarWorks\StatsBundle\dao\ItemSetInformationRetriever;
use Solarium;

class TotalItemViewsRetrieverSolrImpl implements ItemSetInformationRetriever {

    private $solrClient;

    public function __construct(Solarium\Client $solrClient) {
        $this->solrClient = $solrClient;
        $this->solrClient->getPlugin('postbigrequest');
    }

    public function retrieveRegionData(Array $items, \DateTime $startDate, \DateTime $endDate) {
        $statsQuery = $this->createStatsQueryString($items, $startDate, $endDate);
        $solrResult = $this->runQuery($statsQuery);
        return $solrResult->getNumFound();
    }

    private function runQuery($statsQuery) {
        $solrClient = $this->solrClient;
        $statisticsQuery = $solrClient->createSelect();

        $statisticsQuery->setQuery($statsQuery);
        $statisticsQuery->setStart(0)->setRows(0);
        $statisticsQuery->setFields(array('id','name'));

        $statsFacetSet = $statisticsQuery->getFacetSet();
        $statsFacetSet->createFacetField('item.views')->setField('statistics_type')->setField("owningItem")->setMinCount(1)->setLimit(10);

        $solrResult = null;
        try {
            $solrResult = $solrClient->select($statisticsQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }

        return $solrResult;
    }

    private function createStatsQueryString(Array $items, \DateTime $startDate, \DateTime $endDate) {
        $statsQuery = "type:2 AND statistics_type:view AND NOT(isBot:true)" .
            " AND time:[" . $startDate->format('Y-m-d\TH:i:s\Z') . " TO " . $endDate->format('Y-m-d\TH:i:s\Z') . "]" .
            " AND ( ";
        $first = true;

        foreach($items as $itemId) {
            if($first == true) {
                $first = false;
            } else {
                $statsQuery .= " OR ";
            }
            $statsQuery .= ("id:" . $itemId);
        }
        $statsQuery .= " )";

        return $statsQuery;
    }
}
