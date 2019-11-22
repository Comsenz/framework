<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * 命令行的名称及签名。
     *
     * @var string
     */
    protected $signature;

    /**
     * 命令行的描述.
     *
     * @var string
     */
    protected $description;

    protected $laravel;

    /**
     * Call another console command.
     *
     * @param string|\Symfony\Component\Console\Command\Command $command
     *
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        return $this->runCommand($command, $arguments, $this->output);
    }

    /**
     * Get the value of a command option.
     *
     * @param null|string $key
     *
     * @return null|array|bool|string
     */
    public function option($key = null)
    {
        if (null === $key) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getLaravel()
    {
        return $this->laravel;
    }

    /**
     * Set the Laravel application instance.
     *
     * @param \Illuminate\Contracts\Container\Container $laravel
     */
    public function setLaravel($laravel)
    {
        $this->laravel = $laravel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName($this->signature)
            ->setDescription($this->description)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->handle();
    }

    /**
     * Fire the command.
     */
    abstract protected function handle();

    /**
     * Did the user pass the given option?
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Send an info message to the user.
     *
     * @param string $message
     */
    protected function info($message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    /**
     * Send an error or warning message to the user.
     *
     * If possible, this will send the message via STDERR.
     *
     * @param string $message
     */
    protected function error($message)
    {
        if ($this->output instanceof ConsoleOutputInterface) {
            $this->output->getErrorOutput()->writeln("<error>{$message}</error>");
        } else {
            $this->output->writeln("<error>{$message}</error>");
        }
    }

    protected function runCommand($command, array $arguments, OutputInterface $output)
    {
        $arguments['command'] = $command;

        return $this->resolveCommand($command)->run(
            $this->createInputFromArguments($arguments),
            $output
        );
    }

    protected function resolveCommand($command)
    {
        if (!class_exists($command)) {
            return $this->getApplication()->find($command);
        }

        $command = new $command();

        if ($command instanceof Command) {
            $command->setApplication($this->getApplication());
        }

        if ($command instanceof self) {
            $command->setLaravel($this->getLaravel());
        }

        return $command;
    }

    protected function createInputFromArguments(array $arguments)
    {
        return tap(new ArrayInput(array_merge($this->context(), $arguments)), function (InputInterface $input) {
            if ($input->hasParameterOption(['--no-interaction'], true)) {
                $input->setInteractive(false);
            }
        });
    }

    /**
     * Get all of the context passed to the command.
     *
     * @return array
     */
    protected function context()
    {
        return collect($this->option())->only([
            'ansi',
            'no-ansi',
            'no-interaction',
            'quiet',
            'verbose',
        ])->filter()->mapWithKeys(function ($value, $key) {
            return ["--{$key}" => $value];
        })->all();
    }
}
