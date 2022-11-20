<?php

namespace Sushibar;

include('SeatGroup.php');

class Table
{
    /**
     * The seatgroups
     *
     * @var array
     */
    private $seatgroups = [];

    /**
     * Maximal amount of seats.
     *
     * @var integer
     */
    private $maxSeats;

    /**
     * Amount of open seats.
     *
     * @var integer
     */
    private $openSeats;

    /**
     * Constructor.
     *
     * @param integer $seats
     */
    public function __construct($seats)
    {
        $this->maxSeats = $seats;
        $this->openSeats = $seats;
        $seatGroup = new SeatGroup();
        $seatGroup->setSize($seats);
        $seatGroup->setEmpty(true);
        $seatGroup->setId(0);
        $this->seatgroups[$seatGroup->getId()] = $seatGroup;
    }

    /**
     * Get the seatgroups.
     *
     * @return array
     */
    public function getSeatgroups()
    {
        return $this->seatgroups;
    }

    /**
     * Set the seatgroups.
     *
     * @param array $seatgroups
     */
    public function setSeatgroups($seatgroups)
    {
        $this->seatgroups = $seatgroups;
    }

    /**
     * Get max seats.
     *
     * @return int
     */
    public function getMaxSeats()
    {
        return $this->maxSeats;
    }

    /**
     * Get the seatgroup by a given id.
     *
     * @param $id
     * @return SeatGroup|null
     */
    public function getSeatGroupById($id)
    {
        return $this->seatgroups[$id];
    }

    /**
     * Set max seats.
     *
     * @param int $maxSeats
     */
    public function setMaxSeats($maxSeats)
    {
        $this->maxSeats = $maxSeats;
    }

    /**
     * Calculate and get the currently open seats.
     *
     * @return int
     */
    public function getOpenSeats()
    {
        $openSeats = 0;
        foreach ($this->seatgroups as $seatgroup) {
            if ($seatgroup->isEmpty()) {
                $openSeats = $openSeats + $seatgroup->getSize();
            }
        }
        $this->openSeats = $openSeats;
        return $this->openSeats;
    }

    /**
     * Set currently open seats.
     *
     * @param int $openSeats
     */
    public function setOpenSeats($openSeats)
    {
        $this->openSeats = $openSeats;
    }

    /**
     * Returns true if there is enough space for customers.
     *
     * @param $amountOfCustomers
     * @return bool
     */
    public function hasSpaceForNewCustomers($amountOfCustomers)
    {
        return $amountOfCustomers <= $this->getOpenSeats();
    }

    /**
     * Compares two seatgroups by their sizes.
     *
     * @param SeatGroup $a First seatgroup.
     * @param SeatGroup $b Second seatgroup.
     * @return int
     */
    private function compareSeatGroupSizes($a, $b) {
        return strcmp($a->getSize(), $b->getSize());
    }

    /**
     * Find the seatgroup with the least seats for a given amount of customers.
     *
     * @param $amountOfCustomers
     * @return false|SeatGroup
     */
    public function findSeatGroup($amountOfCustomers)
    {
        uasort($this->seatgroups, array($this, 'compareSeatGroupSizes'));

        foreach ($this->seatgroups as $seatgroup) {
            if ($seatgroup->isEmpty() && $seatgroup->getSize() >= $amountOfCustomers) {
                return $seatgroup;
            }
        }
        return false;
    }

    /**
     * Fills a seatgroup with the given amount of customers and splits up an empty seatgroup if needed.
     *
     * @param integer $seatGroupId
     * @param integer $amountOfCusomters
     * @return void
     */
    public function fillSeatGroup($seatGroupId, $amountOfCusomters)
    {
        $seatgroup = $this->seatgroups[$seatGroupId];
        unset($this->seatgroups[$seatGroupId]);
        if ($seatgroup->getSize() > $amountOfCusomters) {
            $this->splitEmptySeatGroup($seatgroup, $seatgroup->getSize() - $amountOfCusomters);
        }
        $seatgroup->setSize($amountOfCusomters);
        $seatgroup->setEmpty(false);
        $this->seatgroups[$seatGroupId] = $seatgroup;
    }

    /**
     * Splits an empty seatgroup from a given seatgroup with the given amount of seats.
     *
     * @param SeatGroup $seatGroup
     * @param integer $amountOfSeatsForNewGroup
     * @return void
     */
    private function splitEmptySeatGroup($seatGroup, $amountOfSeatsForNewGroup)
    {
        $newSeatGroup = new SeatGroup();
        $newId = $seatGroup->getId() + ($seatGroup->getSize() - $amountOfSeatsForNewGroup);
        $newSeatGroup->setId($newId);
        $newSeatGroup->setSize($amountOfSeatsForNewGroup);
        $newSeatGroup->setEmpty(true);
        $this->seatgroups[$newSeatGroup->getId()] = $newSeatGroup;

    }

    /**
     * Empties a given seatgroup.
     *
     * @param $seatgroupToEmpty
     * @return void
     */
    public function emptySeatGroup($seatgroupToEmpty)
    {
        foreach ($this->seatgroups as $seatgroup) {
            if ($seatgroup->getId() == $seatgroupToEmpty->getId()) {
                $seatgroup->setEmpty(true);
            }
        }
    }

    /**
     * Merge two neighboring empty seatgroups to one empty seatgroup.
     *
     * @return void
     */
    public function mergeEmptySeatGroups()
    {
        if (sizeof($this->seatgroups) <= 1) {
            return;
        }

        foreach ($this->seatgroups as $seatgroup) {
            $idOfNextSeatGroup = ($seatgroup->getId() + $seatgroup->getSize()) % $this->maxSeats;
            if (sizeof($this->seatgroups) <= $idOfNextSeatGroup) {
                return;
            }
            $nextSeatGroup = $this->seatgroups[$idOfNextSeatGroup];
            if ($seatgroup->isEmpty() && $nextSeatGroup->isEmpty()) {
                $newSeatGroupSize = $seatgroup->getSize() + $nextSeatGroup->getSize();
                $seatgroup->setSize($newSeatGroupSize);
                unset($this->seatgroups[$idOfNextSeatGroup]);
                return;
            }
        }
    }

    /**
     * Prints some statistics about the table.
     *
     * @return void
     */
    public function printStatistics()
    {
        echo "----------------------\n";
        echo $this->getOpenSeats() . " Sitze sind frei\n";
        echo $this->maxSeats - $this->getOpenSeats() . " Sitze sind besetzt\n";
        $amountOfEatingGroups = 0;
        foreach ($this->seatgroups as $seatgroup) {
            if (!$seatgroup->isEmpty()) {
                $amountOfEatingGroups++;
            }
        }
        echo $amountOfEatingGroups . " Gruppen essen gerade\n";
        echo "----------------------\n";
    }

    /**
     * Returns true if there are no empty seatgroups at the moment.
     *
     * @return bool
     */
    public function hasEatingGroups()
    {
        foreach ($this->seatgroups as $seatgroup) {
            if (!$seatgroup->isEmpty()) {
                return true;
            }
        }
        return false;
    }
}