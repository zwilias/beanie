<?php


namespace Beanie;


interface Command
{
    const VALID_NAME_REGEX = '/^[A-Za-z0-9+\/;.$_()][A-Za-z0-9+\/;.$_()\-]*$/';

    const COMMAND_BURY = 'bury';
    const COMMAND_DELETE = 'delete';
    const COMMAND_IGNORE = 'ignore';
    const COMMAND_KICK = 'kick';
    const COMMAND_KICK_JOB = 'kick-job';
    const COMMAND_LIST_TUBES = 'list-tubes';
    const COMMAND_LIST_TUBES_WATCHED = 'list-tubes-watched';
    const COMMAND_LIST_TUBE_USED = 'list-tube-used';
    const COMMAND_PAUSE_TUBE = 'pause-tube';
    const COMMAND_PEEK = 'peek';
    const COMMAND_PEEK_BURIED = 'peek-buried';
    const COMMAND_PEEK_DELAYED = 'peek-delayed';
    const COMMAND_PEEK_READY = 'peek-ready';
    const COMMAND_PUT = 'put';
    const COMMAND_QUIT = 'quit';
    const COMMAND_RELEASE = 'release';
    const COMMAND_RESERVE = 'reserve';
    const COMMAND_RESERVE_WITH_TIMEOUT = 'reserve-with-timeout';
    const COMMAND_STATS = 'stats';
    const COMMAND_STATS_JOB = 'stats-job';
    const COMMAND_STATS_TUBE = 'stats-tube';
    const COMMAND_TOUCH = 'touch';
    const COMMAND_USE = 'use';
    const COMMAND_WATCH = 'watch';

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
