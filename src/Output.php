<?php

namespace FFMpeg;

class Output {
    public $ffmpeg;
    protected $parameters = [];
    public $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function buildCommand(): array
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

    public function params(array | null $parameters): self
    {
        // Allow null to be passed and ignored
        if (!$parameters) return $this;

        $this->parameters = array_merge(array_filter($parameters, fn($p) => $p), $this->parameters);

        return $this;
    }

    // If the parent FFMpeg instance is set, proxy method calls to it to allow for chaining.
    public function __call(string $method, array $arguments)
    {
        if (!$this->ffmpeg) throw new \Exception(sprintf("Call to undefined method %s::%s()", __CLASS__, $method));
        return call_user_func([$this->ffmpeg, $method], ...$arguments);
    }
}