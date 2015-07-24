<?php


namespace Beanie\Command\CommandLineCreator;


interface CommandLineCreator
{
    /**
     * @return string
     */
    public function getCommandLine();

    /**
     * @return bool
     */
    public function hasData();

    /**
     * @return string
     */
    public function getData();
}
