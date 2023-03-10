<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Command\Redis;

use Mohamed205\voxum\lib\predis\Command\Command as RedisCommand;

/**
 * @link http://redis.io/commands/eval
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class EVAL_ extends RedisCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EVAL';
    }

    /**
     * Calculates the SHA1 hash of the body of the script.
     *
     * @return string SHA1 hash.
     */
    public function getScriptHash()
    {
        return sha1($this->getArgument(0));
    }
}
