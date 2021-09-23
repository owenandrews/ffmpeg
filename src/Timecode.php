<?php

namespace OwenAndrews\FFMpeg;

use FFMpeg\Exceptions\InvalidArgumentException;

class Timecode {
    public $hours;
    public $minutes;
    public $seconds;
    public $frames;

    public function __construct($hours, $minutes, $seconds, $frames)
    {
        if ($minutes > 59) {
            throw new InvalidArgumentException("Argument [\$minutes] cannot be greater than 59.");
        }

        if ($seconds > 59) {
            throw new InvalidArgumentException("Argument [\$seconds] cannot be greater than 59.");
        }

        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
        $this->frames = $frames;
    }

    public static function parse($input): self
    {
        if ($input instanceof self) {
            return $input;
        }

        // Matches 12:40:15 or 40:15 and can include frames 20:00.10
        if (preg_match("/^(?:(?<hours>[0-9]{2}):)?(?<minutes>[0-5]?[0-9]):(?<seconds>[0-5]?[0-9])(?:\.(?<frames>[0-9]+))?$/", trim($input), $matches)) {
            return new static(intval($matches["hours"]), intval($matches["minutes"]), intval($matches["seconds"]), isset($matches["frames"]) ? intval($matches["frames"]) : 0);
        }
        
        // Matches seconds and frames only, i.e 40 or 40.10 or 300
        else if (preg_match("/^(?<seconds>[0-9]*)(?:\.(?<frames>[0-9]+))?$/", trim($input), $matches)) {
            return self::fromSeconds($input);
        }

        throw new InvalidArgumentException(sprintf("Unable to parse timecode: %s", $input));
    }

    public static function fromSeconds($input): self
    {
        $matches = [];

        // Matches seconds and frames only, i.e 40 or 40.10 or 300
        if (preg_match("/^(?<seconds>[0-9]*)(?:\.(?<frames>[0-9]+))?$/", trim($input), $matches)) {
            $minutes = $hours = $frames = 0;

            $frames = isset($matches["frames"]) ? intval($matches["frames"]) : 0;
            $seconds = intval($matches["seconds"]);

            if ($seconds > 59) {
                $minutes = floor($seconds / 60);
                $seconds = $seconds % 60;
            }

            if ($minutes > 59) {
                $hours = floor($minutes / 60);
                $minutes = $minutes % 60;
            }

            return new static($hours, $minutes, $seconds, $frames);
        }

        throw new InvalidArgumentException(sprintf("Unable to parse seconds: %s", $input));
    }

    public function toSeconds(): int {
        $seconds = 0;

        $seconds += $this->hours * 60 * 60;
        $seconds += $this->minutes * 60;
        $seconds += $this->seconds;

        // TODO: Handle frames?
        return (int) $seconds;
    }

    public function __toString(): string
    {
        return sprintf('%02d:%02d:%02d.%02d', $this->hours, $this->minutes, $this->seconds, $this->frames);
    }
}