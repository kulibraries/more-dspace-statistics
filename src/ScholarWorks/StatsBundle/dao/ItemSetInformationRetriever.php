<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/20/15
 * Time: 2:46 PM
 */

namespace ScholarWorks\StatsBundle\dao;


interface ItemSetInformationRetriever {

    public function retrieveRegionData(Array $items, \DateTime $startDate, \DateTime $endDate);
}