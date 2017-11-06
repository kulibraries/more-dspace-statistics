<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/20/15
 * Time: 11:43 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl\authors;

use ScholarWorks\StatsBundle\dao\impl\JsonRetriever;
use ScholarWorks\StatsBundle\dao\ItemSetInformationRetriever;
use ScholarWorks\StatsBundle\models\Item;
use Psr\Log\LoggerInterface;
use Solarium;


class BitstreamInformationRetrieverImpl implements ItemSetInformationRetriever {

    private $logger;
    private $solrClient;
    private $jsonRetriever;

    public function __construct(Solarium\Client $solrClient, JsonRetriever $jsonRetriever, LoggerInterface $logger ) {
        $this->jsonRetriever = $jsonRetriever;
        $this->logger = $logger;
        $this->solrClient = $solrClient;
        $this->solrClient->getPlugin('postbigrequest');
    }

    public function retrieveRegionData(Array $items, \DateTime $startDate, \DateTime $endDate) {
        $topTen = array();

        $statsQuery = $this->createStatsQueryString($items, $startDate, $endDate);
        $solrResult = $this->runQuery($statsQuery);

        $this->logger->debug("[BitstreamInformationRetrieverImpl:retrieveRegionData] Post initial Solr Query Results:  ",
            array("items" => $items, "start" => $startDate, "end" => $endDate, "result" => $solrResult));

        $topTen['totalBitstreamDownloads'] = $solrResult->getNumFound();
        $topTen['topTenDownloads'] = $this->retrieveTopTenInformation($solrResult);

        return $topTen;
    }

    private function retrieveTopTenInformation($solrResult)
    {
        $topTenResult = array();
        $owningItemFacet = $solrResult->getFacetSet()->getFacet('author.information');

        foreach ($owningItemFacet as $itemId => $count) {
            $itemJson = $this->jsonRetriever->retrieveJsonData("rest/items/{$itemId}");

            $itemObject = new Item(
                $itemId,
                $itemJson['name'],
                $count,
                "http://hdl.handle.net/{$itemJson['handle']}",
                array()
            );

            array_push($topTenResult, $itemObject);
        }
        return $topTenResult;
    }


    private function runQuery($statsQueryString) {

        /** @var Solarium\Client $solrClient */
        $solrClient = $this->solrClient;

        /** @var Solarium\QueryType\Select\Query\Query $statisticsQuery */
        $statisticsQuery = $solrClient->createSelect();

        $this->logger->debug("[BitstreamInformationRetriever:runQuery] - statsQuery:  ", array($statsQueryString));
        $statisticsQuery->setQuery($statsQueryString);
        $statisticsQuery->setStart(0)->setRows(0);
        $statisticsQuery->setFields(array('id','name'));

        $statsFacetSet = $statisticsQuery->getFacetSet();
        $statsFacetSet->createFacetField('author.information')
            ->setField('statistics_type')
            ->setField("owningItem")
            ->setMinCount(1)
            ->setLimit(10);

        $solrResult = null;
        try {
            $solrResult = $solrClient->select($statisticsQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }

        return $solrResult;
    }

    private function createStatsQueryString(Array $items, \DateTime $startDate, \DateTime $endDate) {

        $statsBitstreamQuery = "type:0 AND bundleName:ORIGINAL AND NOT(isBot:true)" .
            " AND time:[" . $startDate->format('Y-m-d\TH:i:s\Z') . " TO " . $endDate->format('Y-m-d\TH:i:s\Z') . "]" .
            " AND ( ";
        $first = true;

        foreach($items as $itemId) {
            if($first == true) {
                $first = false;
            } else {
                $statsBitstreamQuery .= " OR ";
            }
            $statsBitstreamQuery .= ("owningItem:" . $itemId);
        }

        $statsBitstreamQuery .= " )";

        return $statsBitstreamQuery;
    }
}