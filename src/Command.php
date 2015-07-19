<?php


namespace Beanie;


interface Command
{
    const VALID_NAME_REGEX = '/^[A-Za-z0-9+\/;.$_()][A-Za-z0-9+\/;.$_()\-]*$/';

    const COMMAND_USE = 'use';

    /**
     * @return string
     */
    function getCommandLine();

    /**
     * @return bool
     */
    function hasData();

    /**
     * @return string
     */
    function getData();
}
