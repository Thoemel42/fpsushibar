<?php

namespace Sushibar;

class SeatGroup
{
    /**
     * Size of the chairgroup
     *
     * @var integer
     */
    private $size;

    /**
     * True if the chairgroup is empty
     *
     * @var bool
     */
    private $empty;

    /**
     * ID of the chairgroup. Number of the chair with the smallest number within the chairgroup.
     *
     * @var integer
     */
    private $id;

    /**
     * Get the size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the size.
     *
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * Returns true if empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->empty;
    }

    /**
     * Set empty.
     *
     * @param bool $empty
     */
    public function setEmpty($empty)
    {
        $this->empty = $empty;
    }

    /**
     * Get the id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}