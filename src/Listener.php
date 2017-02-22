<?php

namespace Pmc\MessageBus;

/**
 * Methods listeners must implement
 * 
 * @author Paul Court <emails@paulcourt.co.uk>
 */
interface Listener
{
    /**
     * Return a list of message class names which this listener wants to receive.
     * 
     * The class names are matched using "instanceof", so it's possible to subscribe
     * to a parent class to receive all child implementations which extend it.
     */
    public function getObservables(): array;
    
    
    /**
     * Receive a message notification
     * 
     * @param $message
     */
    public function notify(Object $message): void;
}
