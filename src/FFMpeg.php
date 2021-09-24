<?php

namespace FFMpeg;

use FFMpeg\Exceptions\CancelProcessException;
use FFMpeg\Exceptions\ProcessFailedException;
use Symfony\Component\Process\Process;

class FFMpeg {
    protected $parameters = [];
    protected $progress;
    protected $timeout = 60;
    public $inputs = [];
    public $outputs = [];

    public function input(string | Input $input): Input
    {
        if ($input instanceof Input) {
            $input->ffmpeg = $this;
            $this->inputs[] = $input;
        } else {
            $input = new Input($input);
            $input->ffmpeg = $this;
            $this->inputs[] = $input;
        }

        return $input;
    }

    public function params(array | null $parameters): self
    {
        // Allow null to passed and ignored
        if (!$parameters) return $this;

        $this->parameters = array_merge(array_filter($parameters, fn($p) => $p), $this->parameters);

        return $this;
    }

    public function output(string | Output $output): Output
    {
        if ($output instanceof Output) {
            $output->ffmpeg = $this;
            $this->outputs[] = $output;
        } else {
            $output = new Output($output);
            $output->ffmpeg = $this;
            $this->outputs[] = $output;
        }

        return $output;
    }

    public function duration(): float
    {
        if (!count($this->inputs)) {
            throw new \Exception("No input streams.");
        }

        return array_reduce($this->inputs, function($duration, Input $input) {
            $duration = max($duration, $input->duration());
            return $duration;
        }, 0);
    }

    public function progress(callable $callback): self
    {
        $this->progress = $callback;
        return $this;
    }

    public function timeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function buildCommand(): array
    {
        $inputs = array_reduce($this->inputs, function($carry, Input $input) {
            return array_merge($carry, $input->buildCommand());
        }, []);

        $outputs = array_reduce($this->outputs, function($carry, Output $output) {
            return array_merge($carry, $output->buildCommand());
        }, []);

        return array_merge($this->parameters, $inputs, $outputs);
    }

    protected function processOutput(string $buffer, float | int $duration, Process $process): void
    {   
        $matches = [];

        if (preg_match("/size=(.*?) time=(?<time>.*?) /", $buffer, $matches)) {
            $currentTime = Timecode::parse($matches["time"]);
            $progress = ($currentTime->toSeconds() / $duration) * 100;

            if ($this->progress) {
                call_user_func($this->progress, $progress, $process);
            }
        }
    }

    public function run(): void
    {
        $duration = $this->duration();

        $process = new Process(["ffmpeg", ...$this->buildCommand()]);
        $process->setTimeout($this->timeout);
        $process->start();

        $process->wait(function ($type, $buffer) use (&$duration, &$process) {
            // FFMpeg sends all logging over stderr
            if ($type === Process::ERR) {
                $this->processOutput($buffer, $duration, $process);
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}