<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/12/14
 * Time: 9:35 AM
 */

namespace ScholarWorks\StatsBundle\models;


class Region {

    private $id;
    private $name;
    private $type;

    public function __construct($id, $type, $name) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getDisplayId() {
        return $this->type . "-" . $this->id;
    }

    public function getDisplayName() {
        return $this->getName() . " (" .$this->getType() . ")";
    }

    public function equal(Region $region) {
        return ($this->getDisplayId() === $region->getDisplayId());
    }


}
