<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Configuration;

/**
 * Default client options container for Predis\Client.
 *
 * Pre-defined options have their specialized handlers that can filter, convert
 * an lazily initialize values in a mini-DI container approach.
 *
 * {@inheritdoc}
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Options implements OptionsInterface
{
    /** @var array */
    protected $handlers = [
        'aggregate' => \Mohamed205\voxum\lib\predis\Configuration\Option\Aggregate::class,
        'cluster' => \Mohamed205\voxum\lib\predis\Configuration\Option\Cluster::class,
        'replication' => \Mohamed205\voxum\lib\predis\Configuration\Option\Replication::class,
        'connections' => \Mohamed205\voxum\lib\predis\Configuration\Option\Connections::class,
        'commands' => \Mohamed205\voxum\lib\predis\Configuration\Option\Commands::class,
        'exceptions' => \Mohamed205\voxum\lib\predis\Configuration\Option\Exceptions::class,
        'prefix' => \Mohamed205\voxum\lib\predis\Configuration\Option\Prefix::class,
        'crc16' => \Mohamed205\voxum\lib\predis\Configuration\Option\CRC16::class,
    ];

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $input;

    /**
     * @param array $options Named array of client options
     */
    public function __construct(array $options = null)
    {
        $this->input = $options ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault($option)
    {
        if (isset($this->handlers[$option])) {
            $handler = $this->handlers[$option];
            $handler = new $handler();

            return $handler->getDefault($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function defined($option)
    {
        return
            array_key_exists($option, $this->options) ||
            array_key_exists($option, $this->input)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($option)
    {
        return (
            array_key_exists($option, $this->options) ||
            array_key_exists($option, $this->input)
        ) && $this->__get($option) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($option)
    {
        if (isset($this->options[$option]) || array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        if (isset($this->input[$option]) || array_key_exists($option, $this->input)) {
            $value = $this->input[$option];
            unset($this->input[$option]);

            if (isset($this->handlers[$option])) {
                $handler = $this->handlers[$option];
                $handler = new $handler();
                $value = $handler->filter($this, $value);
            } elseif (is_object($value) && method_exists($value, '__invoke')) {
                $value = $value($this);
            }

            return $this->options[$option] = $value;
        }

        if (isset($this->handlers[$option])) {
            return $this->options[$option] = $this->getDefault($option);
        }

        return;
    }
}
