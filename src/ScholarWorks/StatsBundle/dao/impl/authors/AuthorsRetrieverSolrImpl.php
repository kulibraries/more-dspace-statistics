<?php


namespace ScholarWorks\StatsBundle\dao\impl\authors;

use ScholarWorks\StatsBundle\dao\RegionDataRetriever;
use ScholarWorks\StatsBundle\models\Region;
use ScholarWorks\StatsBundle\dao\impl\TypeFilter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Solarium;


class AuthorsRetrieverSolrImpl implements RegionDataRetriever {

    private $solrClient;
    private $appCache;
    public $cacheTTL;           // set via services.yml property

    public function __construct(Solarium\Client $solrClient, AdapterInterface $appCache) {
        $this->solrClient = $solrClient;
        $this->appCache = $appCache;
    }

    public function getRegions()
    {
        $cachedAuthors = $this->appCache->getItem("authors_array");
        if (!$cachedAuthors->isHit()) {
            $authorRegions = array();
            $limit = 5000;
            $offset = 0;
            while(true) {
                $solrQuery = $this->solrClient->createSelect();
                $solrQuery->setQuery("*:*");
                $solrQuery->setStart(0);
                $solrQuery->setRows(0);
                $solrQuery->getFacetSet()
                    ->createFacetField("author_keyword")
                    ->setField("author_keyword")
                    ->setMinCount(1)
                    ->setLimit($limit)
                    ->setOffset($offset)
                    ->setSort("name");

                $solrResult = null;
                try {
                    $solrResult = $this->solrClient->execute($solrQuery);
                } catch (\Exception $e) {
                    throw new \Exception("Can't connect to SOLR");
                }
                $json = $solrResult->getData();
                $numFound = 0;
                for ($i = 0; $i < count($json['facet_counts']['facet_fields']['author_keyword']); $i += 2) {
                    $authorName = $json['facet_counts']['facet_fields']['author_keyword'][$i];
                    if (trim($authorName) == true) {
                        $region = new Region($authorName, TypeFilter::$author, $authorName);
                        array_push($authorRegions, $region);
                        $numFound++;
                    }
                }

                if ($numFound==0 or $numFound<($limit-1)) {
                    break;
                }
                $offset += $limit;
            }

            $cachedAuthors->set($authorRegions);
            if ($this->cacheTTL) {
                $cachedAuthors->expiresAfter(\DateInterval::createFromDateString($this->cacheTTL));
            }

            $this->appCache->save($cachedAuthors);
            return $authorRegions;
        }
        else {
            return $cachedAuthors->get();
        }
    }
}
