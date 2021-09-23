<?php

use FFMpeg\FFProbe;
use FFMpeg\Stream;

beforeEach(function () {
    $this->ffprobe = new FFProbe(__DIR__.'/../static/video.mp4');
});

it('can be constructed', function () {
    expect($this->ffprobe)->toBeInstanceOf(FFProbe::class);
});

it('has a video stream', function () {
    expect($this->ffprobe->video())->toBeInstanceOf(Stream::class);
});

it('has correct width', function () {
    expect($this->ffprobe->video()->width)->toEqual(640);
});

it('has correct height', function () {
    expect($this->ffprobe->video()->height)->toEqual(360);
});

it('has correct area', function () {
    expect($this->ffprobe->video()->area())->toEqual(230400);
});

it('has an audio stream', function () {
    expect($this->ffprobe->audio())->toBeInstanceOf(Stream::class);
});