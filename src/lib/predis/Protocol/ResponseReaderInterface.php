<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Protocol;

use Mohamed205\voxum\lib\predis\Connection\CompositeConnectionInterface;

/**
 * Defines a pluggable reader capable of parsing responses returned by Redis and
 * deserializing them to PHP objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ResponseReaderInterface
{
    /**
     * Reads a response from a connection to Redis.
     *
     * @param CompositeConnectionInterface $connection Redis connection.
     *
     * @return mixed
     */
    public function read(CompositeConnectionInterface $connection);
}
