<?php


namespace Beanie;


interface Oath
{
    /**
     * @return resource
     */
    public function getSocket();

    /**
     * @return mixed
     */
    public function invoke();
}
