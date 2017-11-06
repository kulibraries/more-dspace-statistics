<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/19/14
 * Time: 2:26 PM
 */

namespace ScholarWorks\StatsBundle\dao\impl;


class TypeFilter {

    public static $collection = "collection";
    public static $community = "community";
    public static $author = "author";

    public function getStatisticsSolrFilter($type, $id) {
        $lowered_type = strtolower($type);
        $result = "";

        if($lowered_type === TypeFilter::$collection) {
            $result = " AND owningColl:" . $id;
        } else if($lowered_type === TypeFilter::$community) {
            $result = " AND owningComm:" . $id;
        } else if($lowered_type === TypeFilter::$author) {
            $result = " AND author_keyword:" . $id;
        }

        return $result;
    }

    public function getSearchSolrFilter($type, $id) {
        $lowered_type = strtolower($type);
        $result = "";

        if($lowered_type === TypeFilter::$collection) {
            $result = " AND location.coll:" . $id;
        } else if($lowered_type === TypeFilter::$community) {
            $result = " AND location.comm:" . $id;
        } else if($lowered_type === TypeFilter::$author) {
            $result = " AND author_keyword:" . $id;
        }

        return $result;
    }
} 