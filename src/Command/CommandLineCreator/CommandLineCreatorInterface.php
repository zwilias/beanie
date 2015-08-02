<?php


namespace Beanie\Command\CommandLineCreator;


interface CommandLineCreatorInterface
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
