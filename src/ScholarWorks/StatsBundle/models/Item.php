<?php
/**
 * Created by PhpStorm.
 * User: dyelar
 * Date: 9/12/14
 * Time: 9:54 AM
 */

namespace ScholarWorks\StatsBundle\models;


class Item {

    private $id;
    private $title = ""; // may be empty, may be only numbers
    private $numberOfBitStreamDownloads;
    private $handle;
    private $authors = array(""); // may be empty


    private function errorIfNull($argument, $message) {
        if(is_null($argument)) {
            throw new \InvalidArgumentException($message);
        }
    }


    public function __construct($id, $title, $numberOfBitStreamDownloads, $handle, array $authors) {

        $this->errorIfNull($id, "id argument to the constructor of the Item class may not be null.");
        $this->errorIfNull($handle, "handle argument to the constructor of the Item class may not be null.");
        $this->errorIfNull($numberOfBitStreamDownloads, "numberOfBitStreamDownloads argument to the constructor of the Item class may not be null.");


        if(!is_int($numberOfBitStreamDownloads)) {
            throw new \InvalidArgumentException("Argument numberOfBitStreamDownloads to Constructor of Item must be an integer.");
        }

        if(!is_string($handle)) {
            throw new \InvalidArgumentException("The handle argument to the constructor must be a string.");
        }

        /* Set Values */

        if(!is_null($title)) {
            $this->title = $title;
        }

        if(is_array($authors) && count($authors) > 0) {
            $this->authors = $authors;
        }

        $this->id = $id;
        $this->handle = $handle;
        $this->numberOfBitStreamDownloads = $numberOfBitStreamDownloads;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getHandle() {
        return $this->handle;
    }

    public function getNumberOfBitStreamDownloads() {
        return number_format($this->numberOfBitStreamDownloads,"0",".", ",");
    }

    public function getAuthors() {
        return $this->authors;
    }

}