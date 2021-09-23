<?php

namespace FFMpeg;

class Output {
    public $ffmpeg;
    protected $parameters = [];
    public $path;

    public function __construct($ffmpeg, $path)
    {
        $this->path = $path;
        $this->ffmpeg = $ffmpeg;
    }

    public function buildCommand()
    {
        return array_merge($this->parameters, [$this->path]);
    }

    public function getValueForParameter(string $key): ?string
    {
        $index = array_search($key, $this->parameters);

        if ($index === false || !array_key_exists($index + 1, $this->parameters)) {
            return null;
        }

        return $this->parameters[$index + 1];
    }

    public function params($parameters = [])
    {
        $this->parameters = array_merge($parameters, $this->parameters);

        return $this;
    }

    public function __call(string $func, array $arguments)
    {
        return call_user_func([$this->ffmpeg, $func], ...$arguments);
    }
}