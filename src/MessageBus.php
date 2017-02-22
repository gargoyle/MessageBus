<?php

namespace Pmc\MessageBus;

/**
 * Maintain a list of listeners and dispatch messages to all required listeners.
 *
 * @author Gargoyle <g@rgoyle.com>
 */
class MessageBus
{

    /**
     *
     * @var Listener[]
     */
    private $listeners;
    private $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->listeners = [];
        $this->logger = $logger;
    }

    /**
     * Dispatch a message to all listeners on the bus which have subscribed to the
     * message class (or a class in it's parent hierarchy)
     */
    public function dispatch(Message $message): void
    {
        $this->logger->debug("Dispatching message",
                [
            'message class' => get_class($message),
            'listeners' => $this->listeners
        ]);

        foreach ($this->listeners as $listener) {
            foreach ($listener->getObservables() as $observableClassName) {
                $this->checkTypeAndNotify($listener, $message, $observableClassName);
            }
        }
    }

    private function checkTypeAndNotify(Listener $listener, Message $message, string $observableClassName)
    {
        if ($message instanceof $observableClassName) {
            try {
                $listener->notify($message);
            } catch (\Throwable $ex) {
                $this->logger->warn("Supressing error from listener on the MessageBus",
                        [
                    'listener class' => get_class($listener),
                    'message class' => get_class($message),
                    'error message' => $ex->getMessage()
                ]);
            }
        }
    }

    /**
     * Add a listener to the bus
     */
    public function addListener(Listener $listener)
    {
        $this->listeners[] = $listener;
    }

}
