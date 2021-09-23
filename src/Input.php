<?php

namespace FFMpeg;

class Input {
    public $ffmpeg;
    protected $parameters = [];
    public $streams = [];
    public $format;
    public $path;

    public function __construct(FFMpeg $ffmpeg, string $path)
    {
        $this->ffmpeg = $ffmpeg;
        $this->path = $path;
        
        $probe = new FFProbe($path);
        $this->streams = $probe->streams;
        $this->format = $probe->format;
    }

    public function duration(): float
    {
        $originalDuration = $duration = $this->originalDuration();
        $ss = $this->getValueForParameter("-ss");
        $to = $this->getValueForParameter("-to");
        $t = $this->getValueForParameter("-t");

        if ($ss) {
            $duration -= Timecode::parse($ss)->toSeconds();
        }

        // -t takes precedence over -to, so exit early if it is set
        if ($t) {
            return min($duration, Timecode::parse($t)->toSeconds());
        }

        if ($to) {
            $duration -= $originalDuration - Timecode::parse($to)->toSeconds();
        }

        return $duration;
    }

    public function originalDuration(): float
    {
        return $this->format["duration"];
    }

    public function buildCommand(): array
    {
        return array_merge($this->parameters, ['-i', $this->path]);
    }

    public function getValueForParameter(string $key): ?string
    {
        $index = array_search($key, $this->parameters);

        if ($index === false || !array_key_exists($index + 1, $this->parameters)) {
            return null;
        }

        return $this->parameters[$index + 1];
    }

    public function params($parameters = []): self
    {
        $this->parameters = array_merge($parameters, $this->parameters);

        return $this;
    }

    public function __call(string $func, array $arguments)
    {
        return call_user_func([$this->ffmpeg, $func], ...$arguments);
    }
}