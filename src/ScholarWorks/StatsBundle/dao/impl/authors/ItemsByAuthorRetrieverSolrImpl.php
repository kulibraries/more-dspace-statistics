<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/16/15
 * Time: 10:41 AM
 */

namespace ScholarWorks\StatsBundle\dao\impl\authors;

use ScholarWorks\StatsBundle\dao\StatsDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use Solarium;


class ItemsByAuthorRetrieverSolrImpl implements StatsDataRetriever
{

    private $solrClient;

    public function __construct(Solarium\Client $solrClient)
    {
        $this->solrClient = $solrClient;
        $this->solrClient->getPlugin('postbigrequest');
    }

    public function retrieveRegionData(
      Region $region,
      \DateTime $startDate,
      \DateTime $endDate
    ) {
        $solrClient = $this->solrClient;

        $author = $region->getName();
        $authorParams = "search.resourcetype:2"
          . " AND author:\"" . $author . "\""
          . " AND NOT withdrawn:true";

        $searchQuery = $solrClient->createSelect();
        $searchQuery->setQuery($authorParams);
        $searchQuery->setStart(0)->setRows(0);
        $searchQuery->setFields(['id', 'name']);

        $facetSet = $searchQuery->getFacetSet();
        $facetSet->createFacetField('item')
          ->setLimit(1000)
          ->setField('search.resourceid');

        // Execute the search...
        $solrResult = null;
        try {
            $solrResult = $solrClient->execute($searchQuery);
        } catch (\Exception $e) {
            throw new \Exception("Can't connect to SOLR");
        }

        $numberOfItems = $solrResult->getNumFound();
        $facet = $solrResult->getFacetSet()->getFacet('item');

        $itemsAssociatedWithAuthor = [];
        $counter = 0;
        foreach ($facet as $itemId => $count) {
            if ($counter < $numberOfItems) {
                array_push($itemsAssociatedWithAuthor, $itemId);
                $counter++;
            } else {
                break;
            }
        }

        return $itemsAssociatedWithAuthor;
    }
}