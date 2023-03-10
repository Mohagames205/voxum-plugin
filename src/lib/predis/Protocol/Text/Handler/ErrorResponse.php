<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Protocol\Text\Handler;

use Mohamed205\voxum\lib\predis\Connection\CompositeConnectionInterface;
use Mohamed205\voxum\lib\predis\Response\Error;

/**
 * Handler for the error response type in the standard Redis wire protocol.
 * It translates the payload to a complex response object for Predis.
 *
 * @link http://redis.io/topics/protocol
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ErrorResponse implements ResponseHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        return new Error($payload);
    }
}
