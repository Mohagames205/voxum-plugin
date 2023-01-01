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
 * Handler for the multibulk response type in the standard Redis wire protocol.
 * It returns multibulk responses as PHP arrays.
 *
 * @link http://redis.io/topics/protocol
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiBulkResponse implements ResponseHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(CompositeConnectionInterface $connection, $payload)
    {
        $length = (int) $payload;

        if ("$length" !== $payload) {
            CommunicationException::handle(new ProtocolException(
                $connection, "Cannot parse '$payload' as a valid length of a multi-bulk response [{$connection->getParameters()}]"
            ));
        }

        if ($length === -1) {
            return;
        }

        $list = array();

        if ($length > 0) {
            $handlersCache = array();
            $reader = $connection->getProtocol()->getResponseReader();

            for ($i = 0; $i < $length; ++$i) {
                $header = $connection->readLine();
                $prefix = $header[0];

                if (isset($handlersCache[$prefix])) {
                    $handler = $handlersCache[$prefix];
                } else {
                    $handler = $reader->getHandler($prefix);
                    $handlersCache[$prefix] = $handler;
                }

                $list[$i] = $handler->handle($connection, substr($header, 1));
            }
        }

        return $list;
    }
}
