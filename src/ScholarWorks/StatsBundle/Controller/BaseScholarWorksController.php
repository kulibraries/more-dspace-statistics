<?php
/**
 * @author Matthew Copeland <matthew@ku.edu>
 * @copyright 2015 University of Kansas
 */

namespace ScholarWorks\StatsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class BaseScholarWorksController
 * @package ScholarWorks\StatsBundle\Controller
 *
 * This controller is provided as a base class for other controllers being used
 * to accept ScholarWorks region and to/from date information.  It provides some
 * common functionality for each of these classes.
 */

class BaseScholarWorksController extends Controller
{
    protected $statisticsService;
    protected $region;
    protected $toDate;
    protected $fromDate;

    /**
     * init()
     *
     * This method initializes the variables of the class from the container
     * and from the request.  It also deals with the Date Business Logic.
     */
    public function baseInit(Request $request)
    {
        $this->statisticsService = $this->get("statisticsService");

        $query = $request->query;

        $this->region = $query->get('region');
        $from = $query->get('from');
        $to = $query->get('to');

        $now = new \DateTime(null);

        $this->toDate = $this->checkDate($to, $now->format("m"), "01", $now->format("Y"), "23", "59", "59");
        $this->fromDate = $this->checkDate($from, $now->format("m") - 1, "01", $now->format("Y"), "00", "00", "00");

        if ($to == null) {
            $diffDate = new \DateInterval("P1D");
            $this->toDate->sub($diffDate);
        }

    }


    /**
     * checkDate()
     *
     * This method checks to see if dateToCheck string is null.  If it is null, it initializes the result to a DateTime
     * object based on the default parameters (month, day, and year).  If it is not null, then the string is converted
     * to a DateTime object.
     *
     * @param string $dateToCheck
     * @param int $month
     * @param int $day
     * @param int $year
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return \DateTime
     */
    protected function checkDate($dateToCheck, $month, $day, $year, $hour, $minute, $second)
    {
        if ($dateToCheck == null) {
            $date = $this->getDate($month, $day, $year, $hour, $minute, $second);
        } else {
            $date = $this->convertDateStringToDateTime($dateToCheck, $hour, $minute, $second);
        }

        return $date;
    }

    /**
     * getDate()
     *
     * Converts a month, day, and year and converts it to a DateTime object.
     *
     *
     * @param int $month
     * @param int $day
     * @param int $year
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return \DateTime object set to the month, day, and year.
     */
    protected function getDate($month, $day, $year, $hour, $minute, $second)
    {
        $date = new \DateTime(null);
        $date->setDate($year, $month, $day);
        $date->setTime($hour, $minute, $second);

        return $date;
    }

    /**
     * convertDateStringToDateTime()
     *
     * Converts a date String of the format m-d-Y into a
     * DateTime object starting at 00:00:00.
     *
     * @param string $date The date string to convert to a DateTime object.
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return \DateTime The converted date.
     */
    protected function convertDateStringToDateTime($date, $hour, $minute, $second)
    {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date . " " . $hour . ":" . $minute . ":" . $second);
        return $date;
    }


}