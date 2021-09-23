<?php

use OwenAndrews\FFMpeg\FFMpeg;
use OwenAndrews\FFMpeg\Input;

beforeEach(function () {
    $this->input = __DIR__.'/../static/video.mp4';
    $this->ffmpeg = new FFMpeg();
});

it('can add input as string', function () {
    expect($this->ffmpeg->input($this->input))->toBeInstanceOf(Input::class);
});

it('can add input as instance', function () {
    $input = new Input($this->ffmpeg, $this->input);
    expect($this->ffmpeg->input($input))->toBe($input);
});

it('can add input params', function () {
    $this->ffmpeg->input($this->input)->params(['-ss', '1', '-t', '1']);
    expect($this->ffmpeg->buildCommand())->toMatchArray(['-ss', '1', '-t', '1', '-i', $this->input]);
});

it('has correct duration', function () {
    $input = $this->ffmpeg->input($this->input);
    $clippedInput = $this->ffmpeg->input($this->input)->params(['-ss', '1', '-t', '1']);
    expect($input->duration())->toEqual(2.176);
    expect($clippedInput->duration())->toEqual(1);
});

it('can output video', function () {
    $this->ffmpeg->input($this->input);
    $this->ffmpeg->output(__DIR__.'/../static/video-out.mp4');
    $this->ffmpeg->run();
});