<?php

namespace Sushibar;

include('ArgumentsChecker.php');
include('Table.php');


use Sushibar\ArgumentsChecker;

$algorithm = new Algorithm();
$algorithm->runAlgorithm();

class Algorithm
{

    /**
     * @var Table
     */
    private $table;

    /**
     * @var ArgumentsChecker
     */
    private $argumentsChecker;

    /**
     * Runs the algorithm for testing.
     *
     * @param $arguments
     * @return void
     */
    public function runAlgorithm()
    {
        echo "Anzahl Stühle wählen:";
        $seatAmount = trim(fgets(STDIN));
        $this->argumentsChecker = new ArgumentsChecker();
        if (!$this->hasCorrectChairInput($seatAmount)) {
            die();
        }
        $this->table = new Table($seatAmount);

        while (1) {
            $this->handleIncommingCustomers();
            $this->table->printStatistics();
            $this->handleLeavingCustomers();
            $this->table->printStatistics();
        }
    }

    /**
     * Handles incomming customers.
     *
     * @return void
     */
    private function handleIncommingCustomers()
    {
        echo "Ankommende Gäste:";
        $amountOfNewCustoners = trim(fgets(STDIN));
        if ($amountOfNewCustoners < 1) {
            echo "Keiner kommt\n";
            return;
        }
        if (!$this->table->hasSpaceForNewCustomers($amountOfNewCustoners)) {
            echo "Nicht genug Plätze für " . $amountOfNewCustoners . " Kunden\n";
            return;
        }

        $seatGroup = $this->table->findSeatGroup($amountOfNewCustoners);
        if (!$seatGroup) {
            echo "Sushichef ist sauer\n";
        } else {
            $this->table->fillSeatGroup($seatGroup->getId(), $amountOfNewCustoners);
        }
    }

    /**
     * Handles leaving customers.
     *
     * @return void
     */
    private function handleLeavingCustomers()
    {
        if (!$this->table->hasEatingGroups()) {
            return;
        }
        $leavingSeatGroupId = $this->handleLeavingCustomersInput();
        if (!is_numeric($leavingSeatGroupId)) {
            echo "niemand geht\n";
            return;
        }
        $leavingSeatGroup = $this->table->getSeatGroupById($leavingSeatGroupId);
        echo $leavingSeatGroup->getSize() . " Kunden gehen\n";
        $this->table->emptySeatGroup($leavingSeatGroup);
        $this->table->mergeEmptySeatGroups();
    }

    /**
     * Handle the input of the leaving seatgroup id and returns it.
     *
     * @return int|void
     */
    private function handleLeavingCustomersInput()
    {
        echo "Gruppennummer zum gehen wählen:\n";
        echo "Mögliche Gruppen:\n";
        $eatingGroups = [];
        foreach ($this->table->getSeatgroups() as $id => $seatgroup) {
            if (!$seatgroup->isEmpty()) {
                echo $seatgroup->getId() . ": mit " . $seatgroup->getSize() . " Leuten\n";
                $eatingGroups[$id] = $seatgroup;
            }
        }
        $leavingSeatGroupId = trim(fgets(STDIN));

        if (!array_key_exists($leavingSeatGroupId, $eatingGroups)) {
            echo "Gruppe nicht vorhanden\n";
            return;
        }

        return $leavingSeatGroupId;
    }

    /**
     * Returns true if input is correct
     *
     * @param integer $input
     * @return void
     */
    private function hasCorrectChairInput($input)
    {
        if ($input === 0) {
            echo "Zu wenig Stühle\n";
            return false;
        }

        if (empty($input)) {
            echo "Bitte Stuhlzahl übergeben\n";
            return false;
        }
        if (!$this->argumentsChecker->checkNaturalNumber($input)) {
            echo "Bitte natürliche Zahl übergeben";
            return false;
        }

        return true;
    }
}