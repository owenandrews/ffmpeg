<?php

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Input;
use Symfony\Component\Filesystem\Filesystem;

beforeEach(function () {
    $this->input = __DIR__.'/../static/video.mp4';
    $this->ffmpeg = new FFMpeg();
});

it('can add input as string', function () {
    expect($this->ffmpeg->input($this->input))->toBeInstanceOf(Input::class);
});

it('can add input as instance', function () {
    $input = new Input($this->input);
    expect($this->ffmpeg->input($input))->toBe($input);
    expect($this->ffmpeg->inputs[0])->toBe($input);
    expect($input->ffmpeg)->toBe($this->ffmpeg);
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
    $filesystem = new Filesystem();
    $output = __DIR__.'/../tmp/video-out.mp4';
    $this->ffmpeg->input($this->input);
    $this->ffmpeg->output($output)
        ->params([
            '-ss', '1',
            '-t', '1',
            '-vf', 'scale=100:-2'
        ]);
    $this->ffmpeg->run();

    expect($filesystem->exists($output))->toBeTrue();

    // Assert correct filters were applied
    $file = new FFProbe($output);
    expect($file->video()->width)->toEqual(100);
    expect($file->video()->height)->toEqual(56);
    expect((int) $file->video()->duration)->toEqual(1);
});