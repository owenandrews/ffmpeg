<?php

namespace FFMpeg;

use Pest\Support\Str;
use Symfony\Component\Process\Process;

class FFProbe {
    public $streams;
    public $format;

    public function __construct($path)
    {
        $process = new Process(["ffprobe", "-v", "error", "-print_format", "json", "-show_format", "-show_streams", $path]);
        $process->mustRun();
        
        $data = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);

        $this->streams = array_map(fn ($stream) => new Stream($stream), $data["streams"]);

        $this->format = $data["format"];
    }

    public function videoStreams(): array
    {
        return array_values(array_filter($this->streams, fn ($stream) => $stream->type() === Stream::TYPE_VIDEO));
    }

    public function audioStreams(): array
    {
        return array_values(array_filter($this->streams, fn ($stream) => $stream->type() === Stream::TYPE_AUDIO));
    }

    public function video(): ?Stream
    {
        $streams = $this->videoStreams();

        if (empty($streams)) return null;

        return $streams[0];
    }

    public function audio(): ?Stream
    {
        $streams = $this->audioStreams();

        if (empty($streams)) return null;

        return $streams[0];
    }
}