<?php

namespace FFMpeg;

use Exception;
use FFMpeg\Exceptions\InvalidArgumentException;

class Stream {
    public const TYPE_VIDEO = "video";
    public const TYPE_AUDIO = "audio";

    public $stream;

    public function __construct($stream)
    {
        $this->stream = $stream;
    }

    public function type(): string
    {
        return $this->stream["codec_type"];
    }

    public function isVideo(): bool
    {
        return $this->type() === self::TYPE_VIDEO;
    }

    public function area(): int
    {
        if (!$this->isVideo()) {
            throw new Exception("Area is unavailable on a non-video stream.");
        }

        return $this->stream["width"] * $this->stream["height"];
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->stream)) {
            return $this->stream[$name];
        }

        throw new InvalidArgumentException(sprintf("Unknown property [%s]", $name));
    }
}