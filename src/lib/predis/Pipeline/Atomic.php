<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Pipeline;

use Mohamed205\voxum\lib\predis\ClientException;
use Mohamed205\voxum\lib\predis\ClientInterface;
use Mohamed205\voxum\lib\predis\Connection\ConnectionInterface;
use Mohamed205\voxum\lib\predis\Connection\NodeConnectionInterface;
use Mohamed205\voxum\lib\predis\Response\ErrorInterface as ErrorResponseInterface;
use Mohamed205\voxum\lib\predis\Response\ResponseInterface;
use Mohamed205\voxum\lib\predis\Response\ServerException;

/**
 * Command pipeline wrapped into a MULTI / EXEC transaction.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Atomic extends Pipeline
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ClientInterface $client)
    {
        if (!$client->getCommandFactory()->supports('multi', 'exec', 'discard')) {
            throw new ClientException(
                "'MULTI', 'EXEC' and 'DISCARD' are not supported by the current command factory."
            );
        }

        parent::__construct($client);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConnection()
    {
        $connection = $this->getClient()->getConnection();

        if (!$connection instanceof NodeConnectionInterface) {
            $class = __CLASS__;

            throw new ClientException("The class '$class' does not support aggregate connections.");
        }

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    protected function executePipeline(ConnectionInterface $connection, \SplQueue $commands)
    {
        $commandFactory = $this->getClient()->getCommandFactory();
        $connection->executeCommand($commandFactory->create('multi'));

        foreach ($commands as $command) {
            $connection->writeRequest($command);
        }

        foreach ($commands as $command) {
            $response = $connection->readResponse($command);

            if ($response instanceof ErrorResponseInterface) {
                $connection->executeCommand($commandFactory->create('discard'));
                throw new ServerException($response->getMessage());
            }
        }

        $executed = $connection->executeCommand($commandFactory->create('exec'));

        if (!isset($executed)) {
            // TODO: should be throwing a more appropriate exception.
            throw new ClientException(
                'The underlying transaction has been aborted by the server.'
            );
        }

        if (count($executed) !== count($commands)) {
            $expected = count($commands);
            $received = count($executed);

            throw new ClientException(
                "Invalid number of responses [expected $expected, received $received]."
            );
        }

        $responses = array();
        $sizeOfPipe = count($commands);
        $exceptions = $this->throwServerExceptions();

        for ($i = 0; $i < $sizeOfPipe; ++$i) {
            $command = $commands->dequeue();
            $response = $executed[$i];

            if (!$response instanceof ResponseInterface) {
                $responses[] = $command->parseResponse($response);
            } elseif ($response instanceof ErrorResponseInterface && $exceptions) {
                $this->exception($connection, $response);
            } else {
                $responses[] = $response;
            }

            unset($executed[$i]);
        }

        return $responses;
    }
}
