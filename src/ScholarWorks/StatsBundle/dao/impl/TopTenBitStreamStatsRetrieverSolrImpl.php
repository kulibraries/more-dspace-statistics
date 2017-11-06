<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/19/14
 * Time: 11:39 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl;

use ScholarWorks\StatsBundle\models\Region;
use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\models\Item;
use Solarium;


class TopTenBitStreamStatsRetrieverSolrImpl implements StatsDataRetriever {

    private $jsonRetriever;
    private $solrClient;

    public function __construct(JsonRetriever $jsonRetriever, Solarium\Client $solrClient)
    {
        $this->jsonRetriever = $jsonRetriever;
        $this->solrClient = $solrClient;
    }

    public function retrieveRegionData(Region $region, \DateTime $startDate, \DateTime $endDate)
    {
        $tf = new TypeFilter();
        $typeQueryPortion = $tf->getStatisticsSolrFilter($region->getType(), $region->getId());
        $timeQueryPortion = "time:[" . $startDate->format('Y-m-d\TH:i:s\Z') . " TO " . $endDate->format('Y-m-d\TH:i:s\Z') . "]";

        $solrClient = $this->solrClient;
        $solrQuery = $solrClient->createSelect();
        $solrQuery->setQuery("*");
        $solrQuery->setStart(0);
        $solrQuery->setRows(0);
        $solrQuery->setFields(array("*", "score"));

        $solrQuery->createFilterQuery('notbot')->setQuery("NOT(isBot:true) " . $typeQueryPortion);
        $solrQuery->createFilterQuery('bundleName')->setQuery("bundleName:ORIGINAL");
        $solrQuery->createFilterQuery('type')->setQuery("type:0");
        $solrQuery->createFilterQuery('timeQuery')->setQuery($timeQueryPortion);
        $solrQuery->createFilterQuery('statistics_type')->setQuery("statistics_type:view");

        $solrQuery->getFacetSet()
            ->createFacetField('owningItem')
            ->setField('owningItem')
            ->setMinCount(1)
            ->setLimit(10);

        $solrResult = null;
        try {
            $solrResult = $solrClient->execute($solrQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }

        $json = $solrResult->getData();
        $topTen = $json['facet_counts']['facet_fields']['owningItem'];
        $itemCount = count($topTen);

        $topTenDownloads = array();

        // Post-Process Loop
        for($i = 0; $i < $itemCount; $i=$i+2) {
            $id = $topTen[$i];
            $numberOfBitStreamDownloads = $topTen[$i + 1];

            $itemUrl = "/rest/items/{$id}";
            $itemInformation = $this->jsonRetriever->retrieveJsonData($itemUrl);

            if ($itemInformation != null) {
                $title = $itemInformation['name'];
                $handle = "http://hdl.handle.net/{$itemInformation['handle']}";

                $item = new Item(
                    $id,
                    $title,
                    $numberOfBitStreamDownloads,
                    $handle,
                    array()
                );

                array_push($topTenDownloads, $item);
            }
        }

        return $topTenDownloads;
    }

}
