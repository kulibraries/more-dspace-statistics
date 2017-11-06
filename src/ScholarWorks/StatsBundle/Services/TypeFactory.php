<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 1/26/15
 * Time: 12:00 PM
 */

namespace ScholarWorks\StatsBundle\Services;


interface TypeFactory {

    public function getRegionStatisticsService($type);

}