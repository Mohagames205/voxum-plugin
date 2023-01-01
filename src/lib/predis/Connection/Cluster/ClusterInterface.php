<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Connection\Cluster;

use Mohamed205\voxum\lib\predis\Connection\AggregateConnectionInterface;

/**
 * Defines a cluster of Redis servers formed by aggregating multiple connection
 * instances to single Redis nodes.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ClusterInterface extends AggregateConnectionInterface
{
}
