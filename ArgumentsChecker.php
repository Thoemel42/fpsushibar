<?php

namespace Sushibar;

class ArgumentsChecker
{

    /**
     * Returns true if the arguments are valid.
     *
     * @param integer $chairAmount
     * @return void
     */
    public function checkNaturalNumber($chairAmount)
    {
        return is_numeric($chairAmount)
            && $chairAmount > 0;
    }

}