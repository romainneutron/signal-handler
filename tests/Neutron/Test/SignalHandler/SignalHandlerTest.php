<?php

namespace Neutron\Test\SignalHandler;

use Neutron\SignalHandler\SignalHandler;

class SignalHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $handler = SignalHandler::getInstance();
        $handler->unregisterAll();
    }

    public function testRegisterSignals()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGCONT, $this->expectCallableNever());
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
    }

    public function testRegisterSignalsMultipleTriggers()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGCONT, $this->expectCallableOnce());
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    public function testRegisterMultipleCallbacks()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGCONT, $this->expectCallableNever());
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
    }

    public function testRegisterMultipleCallbacksMultipleTriggers()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGIO, $this->expectCallableOnce());
        $handler->register(SIGCONT, $this->expectCallableOnce());
        $handler->register(SIGCONT, $this->expectCallableOnce());
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    public function testRegisterAndUnregisterNamespace()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableOnce(), 'namespace2');
        $handler->register(SIGCONT, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGCONT, $this->expectCallableOnce(), 'namespace2');
        $handler->unregisterNamespace('namespace');
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    public function testRegisterAndUnregisterNamespaceWithSignal()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableOnce(), 'namespace2');
        $handler->register(SIGCONT, $this->expectCallableOnce(), 'namespace');
        $handler->register(SIGCONT, $this->expectCallableOnce(), 'namespace2');
        $handler->unregisterNamespace('namespace', SIGIO);
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    public function testRegisterAndUnregisterSignal()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace2');
        $handler->register(SIGCONT, $this->expectCallableOnce(), 'namespace');
        $handler->register(SIGCONT, $this->expectCallableOnce(), 'namespace2');
        $handler->unregisterSignal(SIGIO);
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    public function testUnregisterAll()
    {
        $handler = SignalHandler::getInstance();
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGIO, $this->expectCallableNever(), 'namespace2');
        $handler->register(SIGCONT, $this->expectCallableNever(), 'namespace');
        $handler->register(SIGCONT, $this->expectCallableNever(), 'namespace2');
        $handler->unregisterAll();
        declare (ticks=1);
        posix_kill(getmypid(), SIGIO);
        posix_kill(getmypid(), SIGCONT);
    }

    protected function expectCallableOnce()
    {
        $callback = $this->createCallableMock();
        $callback
            ->expects($this->once())
            ->method('__invoke');

        return $callback;
    }

    protected function expectCallableNever()
    {
        $callback = $this->createCallableMock();
        $callback
            ->expects($this->never())
            ->method('__invoke');

        return $callback;
    }

    protected function createCallableMock()
    {
        return $this->getMock('Neutron\Test\SignalHandler\CallableStub');
    }
}
