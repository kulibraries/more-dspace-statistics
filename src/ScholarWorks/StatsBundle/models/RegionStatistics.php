<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\models;

class RegionStatistics {
    private $region;
    private $startDate;
    private $endDate;
    private $totalBitStreamDownloads = 0;
    private $totalItemViews = 0;
    private $topTenDownloads = array();
    private $numberOfItemsInRegion = 0;

    /**
     * Constructor Helper Function for determining if a variable is an integer, and if not, generating an exception with the
     * passed message.  It also spits out what the original argument was for debugging purposes.
     *
     * @param string $argument The variable to be tested.
     * @param string $message The message to print, if the argument fails the test.
     * @throws \InvalidArgumentException this exception is thrown on failure to pass the test.
     */
    private function failIfNotInt($argument, $message) {
        if(!is_int($argument) ) {
            throw new \InvalidArgumentException($message . "  Input was " . $argument);
        }
    }

    /**
     * Constructor Helper Function for determining if an array is an array of instances of the Item class.  If any element of the
     * array is not an instance of the item class, then the test fails, and it generates an exception with the message argument.
     *
     * @param array $array The array to be tested.
     * @param string $message The message to print, if the array fails the test.
     * @throws \InvalidArgumentException this exception is thrown on failure to pass the test.
     */
    private function failIfNotArrayOfItems(array $array, $message) {

        if(count(array_filter($array, function($object) {
                return ! ($object instanceof Item);
            })) > 0 ) {
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Constructor
     *
     * @param Region $region Information about the specific community, collection, or repository.
     * @param \DateTime $startDate The starting date from which statistics are based.
     * @param \DateTime $endDate The ending date from which statistics are based.
     * @param integer $totalBitStreamDownloads The total number of bit stream downloads for the specified region for the specified time frame.
     * @param integer $totalItemViews The total number of item views for the specified region for the specified time frame.
     * @param array $topTenDownloads A sorted array of Item objects, where the array is sorted based on rank among the top ten downloads for the specified time frame for the specified region.
     * @param integer $numberOfItemsInRegion The total number of items found in the region at the end of the specified time period.
     */
    public function __construct(Region $region, \DateTime $startDate, \DateTime $endDate,
                                $totalBitStreamDownloads, $totalItemViews,
                                 array $topTenDownloads,
                                 $numberOfItemsInRegion) {
/*
        $this->failIfNotInt($totalBitStreamDownloads, "The total bit stream downloads argument to the constructor must be an integer.");
        $this->failIfNotInt($totalItemViews,"The total item views argument to the constructor must be an integer.");
        $this->failIfNotInt($numberOfItemsInRegion, "The Total number of items in the region for the current time period must be an integer.");
*/
        $this->failIfNotArrayOfItems($topTenDownloads, "The top ten downloads current month argument was not exclusively an array of items.");

        $this->region = $region;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalBitStreamDownloads = $totalBitStreamDownloads;
        $this->totalItemViews = $totalItemViews;
        $this->topTenDownloads = $topTenDownloads;
        $this->numberOfItemsInRegion = $numberOfItemsInRegion;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function getTotalBitStreamDownloads() {
        return number_format($this->totalBitStreamDownloads,0,".",",");
    }

    public function getTotalItemViews() {
        return number_format($this->totalItemViews,0,".",",");
    }

    public function getArrayOfTopTenDownloads() {
        return $this->topTenDownloads;
    }

    public function getNumberOfItemsInRegion() {
        return number_format($this->numberOfItemsInRegion,0,".",",");
    }

    public function getAverageNumberOfDownloadsPerItem() {
        $result = "0";

        if($this->numberOfItemsInRegion > 0) {
            $result = (int) round($this->totalBitStreamDownloads  / $this->numberOfItemsInRegion );
        }
        return number_format($result,0,".",",");
    }
}