<?php
namespace Grasshopper;


interface Error
{
    /**
     * Get error number
     *
     * @return int
     */
    public function getNo();

    /**
     * Get error message
     *
     * @return int
     */
    public function getMessage();
}