<?php
/**
 * @author Jeremy Keeler <jeremykeeler@ku.edu>
 * @copyright 2017 The University of Kansas
 */

namespace ScholarWorks\StatsBundle\Services\impl;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Solarium\Client as SolariumClient;


class DateService
{
    /* The following property variables must be set in services.yml properties section. */

    /** @var \DateTime $dateStatsStart */
    public $dateStatsStart;

    /** @var int $cacheTTL */
    public $cacheTTL;


    /* The following service/object variables must be supplied to DateService constructor. */

    /** @var SolariumClient $solrClient */
    public $solrClient;

    /** @var AdapterInterface $appCache */
    public $appCache;


    /**
     * DateService Constructor
     *
     * We are using two service objects in this class. It would be nice to be able
     * to pass them in via the properties section in services.yml, but that's not
     * how this works. We have to pass them as arguments to the constructor. So
     * we need a constructor.
     */

    public function __construct(SolariumClient $solrClient, AdapterInterface $appCache)
    {
        $this->solrClient = $solrClient;
        $this->appCache = $appCache;
    }


    /**
     * getDateStatsStart()
     *
     * This method returns the "Date Statistics Started To Be Gathered" system-wide property,
     * henceforth to be known as 'dateStatsStart'. This property is used to select time ranges
     * in calls to SOLR, and also to set the range of the date picker UI.
     *
     * Grizzly details:
     *   There are 2 places where the dateStatsStart property can be defined:
     *      1. In parameters.yml as dateStatisticsStartedToBeGathered
     *      2. In SOLR. Query for the date of the oldest record in the statistics endpoint. The value
     *         returned from SOLR is then stored in the Symfony app cache under key 'dateStatsStart'
     *
     * @Return \DateTime
     */

    public function getDateStatsStart()
    {
        if ($this->dateStatsStart == null) {

            # dateStatsStart was not specified in parameters.yml, so we need to
            # query SOLR for the oldest record. But, first, check the app cache...

            $cached_dateStatsStart = $this->appCache->getItem("dateStatsStart");

            if (!$cached_dateStatsStart->isHit()) {
                # dateStatsStart is NOT cached. This must be our first time here.

                # Construct SOLR query...
                $solrQuery = $this->solrClient->createSelect();
                $solrQuery->setQuery("*:*");
                $solrQuery->setFields(['time']);
                $solrQuery->setStart(0);
                $solrQuery->setRows(1);
                $solrQuery->addSort("time", "asc");

                # Execute SOLR query or die
                $solrResult = null;
                try {
                    $solrResult = $this->solrClient->execute($solrQuery);
                } catch (\Exception $e) {
                    throw new \Exception("Can't connect to SOLR");
                }

                # Retrieve data from SOLR results...
                $json = $solrResult->getData();
                $earliestDate = new \Datetime($json['response']['docs'][0]['time']);

                # Make 'earliest date' start at the beginning of the month...
                $earliestDate->setTime(0, 0, 0);
                $days = $earliestDate->format('d');
                $days -= 1;
                $this->dateStatsStart = $earliestDate->sub(new \DateInterval("P{$days}D"));

                # Save dateStatsStart into the app cache...
                $cached_dateStatsStart->set($this->dateStatsStart);
                if ($this->cacheTTL) {
                    $cached_dateStatsStart->expiresAfter(\DateInterval::createFromDateString($this->cacheTTL));
                }
                $this->appCache->save($cached_dateStatsStart);

            } else {
                # Using previously cached copy of dateStatsStart...
                $this->dateStatsStart = $cached_dateStatsStart->get();
            }
        } else {
            # convert dateStatsStart to \DateTime object
            if (is_string($this->dateStatsStart)) {
                $this->dateStatsStart = new \DateTime($this->dateStatsStart);
            }
        }

        return $this->dateStatsStart;
    }

}