<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neutron\SignalHandler;

class SignalHandler
{
    const DEFAULT_NAMESPACE = '__default__';

    private static $_instance;
    private $signals = array();

    private function __construct() {}

    /**
     * Registers as callback for some signals.
     *
     * @param integer|array $signals   A signal or an array of signals.
     * @param callable      $callback  A callback to execute on signal.
     * @param string        $namespace A namespace where to store the callback.
     *
     * @return SignalHandler
     *
     * @api
     */
    public function register($signals, $callback, $namespace = self::DEFAULT_NAMESPACE)
    {
        if (!is_array($signals)) {
            $signals = array($signals);
        }

        foreach ($signals as $signal) {
            $this->registerSignal($signal, $callback, $namespace);
        }

        return $this;
    }

    /**
     * Unregisters callbacks given a namespace, optionally a signal.
     *
     * @param string  $namespace
     * @param integer $signal
     *
     * @api
     */
    public function unregisterNamespace($namespace, $signal = null)
    {
        foreach (array_keys($this->signals) as $registeredSignal) {
            if (null !== $signal && $signal !== $registeredSignal) {
                continue;
            }
            if (isset($this->signals[$registeredSignal][$namespace])) {
                unset ($this->signals[$registeredSignal][$namespace]);
                $this->cleanupHandle($registeredSignal);
            }
        }
    }

    /**
     * Unregisters callbacks given a signal.
     *
     * @param integer $signal
     *
     * @api
     */
    public function unregisterSignal($signal)
    {
        if (!isset($this->signals[$signal])) {
            return;
        }

        $this->signals[$signal] = array();
        $this->cleanupHandle($signal);
    }

    /**
     * Unregisters all handlers.
     *
     * @api
     */
    public function unregisterAll()
    {
        foreach (array_keys($this->signals) as $signal) {
            $this->unregisterSignal($signal);
        }
    }

    /**
     * Signal handler.
     *
     * @param integer $signal
     */
    public function signalHandler($signal)
    {
        if (!isset($this->signals[$signal])) {
            return;
        }

        foreach ($this->signals[$signal] as $callbacks) {
            foreach ($callbacks as $callback) {
                call_user_func($callback, $signal);
            }
        }
    }

    /**
     * Returns the singleton.
     *
     * @return SignalHandler
     *
     * @api
     */
    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    private function registerSignal($signal, $callback, $namespace)
    {
        if (true === $new = !isset($this->signals[$signal])) {
            $this->signals[$signal] = array($namespace => array());
        }
        $this->signals[$signal][$namespace][] = $callback;

        if ($new) {
            pcntl_signal($signal, array($this, 'signalHandler'));
        }
    }

    private function cleanupHandle($signal)
    {
        if (0 === count($this->signals[$signal])) {
            pcntl_signal($signal, SIG_DFL);
            unset($this->signals[$signal]);
        }
    }
}
