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

use Mohamed205\voxum\lib\predis\CommunicationException;
use Mohamed205\voxum\lib\predis\Connection\CompositeConnectionInterface;
use Mohamed205\voxum\lib\predis\Protocol\ProtocolException;

/**
 * Handler for the bulk response type in the standard Redis wire protocol.
 * It translates the payload to a string or a NULL.
 *
 * @link http://redis.io/topics/protocol
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class BulkResponse implements ResponseHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        $length = (int) $payload;

        if ("$length" !== $payload) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid length for a bulk response [{$connection->getParameters()}]"
            ));
        }

        if ($length >= 0) {
            return substr($connection->readBuffer($length + 2), 0, -2);
        }

        if ($length == -1) {
            return;
        }

        CommunicationException::handle(new ProtocolException(
            $connection, "Value '$payload' is not a valid length for a bulk response [{$connection->getParameters()}]"
        ));

        return;
    }
}
