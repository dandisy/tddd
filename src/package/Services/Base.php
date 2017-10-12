<?php

namespace PragmaRX\TestsWatcher\Package\Services;

use JasonLewis\ResourceWatcher\Event;

class Base
{
    /**
     * The command object.
     *
     * @object \PragmaRX\TestsWatcher\Package\Console\Commands\BaseCommand
     */
    protected $command;

    /**
     * Get a configuration key.
     *
     * @param $key
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function getConfig($key)
    {
        if (is_null($value = config("ci.{$key}"))) {
            throw new \Exception("The configuration key '{$key}' was not defined.");
        }

        return $value;
    }

    /**
     * Show progress in terminal.
     *
     * @param $line
     */
    public function showProgress($line, $addSeparator = false)
    {
        if ($addSeparator) {
            $this->command->drawLine($line = 'Executing '.$line);
        }

        $this->command->line($line);
    }

    /**
     * Show a comment in terminal.
     *
     * @param $comment
     */
    public function showComment($comment)
    {
        $this->command->comment($comment);
    }

    /**
     * Get the event name.
     *
     * @param $eventCode
     *
     * @return string
     */
    protected function getEventName($eventCode)
    {
        $event = '(unknown event)';

        switch ($eventCode) {
            case Event::RESOURCE_DELETED:
                $event = 'deleted';
                break;
            case Event::RESOURCE_CREATED:
                $event = 'created';
                break;
            case Event::RESOURCE_MODIFIED:
                $event = 'modified';
                break;
        }

        return $event;
    }

    protected function displayMessages($messages)
    {
        $fatal = $messages->reduce(function ($carry, $message) {
            $prefix = $message['type'] == 'error' ? 'FATAL ERROR: ' : '';

            $this->command->{$message['type']}($prefix.$message['body']);

            if ($message['type'] == 'error') {
                return true;
            }

            return $carry;
        });

        if ($fatal == true) {
            die;
        }
    }
}