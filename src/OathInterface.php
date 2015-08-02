<?php


namespace Beanie;


interface OathInterface
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
