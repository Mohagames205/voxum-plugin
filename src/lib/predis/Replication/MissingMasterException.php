<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Replication;

use Mohamed205\voxum\lib\predis\ClientException;

/**
 * Exception class that identifies when master is missing in a replication setup.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MissingMasterException extends ClientException
{
}
