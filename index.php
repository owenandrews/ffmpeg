<?php

require_once __DIR__.'/vendor/autoload.php';

use FFMpeg\Exceptions\CancelProcessException;
use FFMpeg\FFMpeg;

$ffmpeg = new FFMpeg();
$ffmpeg
    ->params(["-y"])
    ->input("/Users/owenandrews/Desktop/ffmpeg-testing/input.mp4")
    ->params([
        "-sn", "-dn",
        "-ss", "15",
        "-t", "10"
    ])
    ->input("/Users/owenandrews/Desktop/ffmpeg-testing/input-2.mp4")
    ->params(["-sn", "-dn"])
    ->output("/Users/owenandrews/Desktop/ffmpeg-testing/output/out-1.mp4")
    ->params([
        "-vf", "scale=720:-2", 
        "-map", "0:v"
    ])
    ->output("/Users/owenandrews/Desktop/ffmpeg-testing/output/out-2.mp4")
    ->params([
        "-vf", "scale=720:-2",
        "-map", "1:v"
    ])
    ->output("/Users/owenandrews/Desktop/ffmpeg-testing/output/poster.jpg")
    ->params([
        "-vf", "scale=200:-2",
        "-frames:v", "1",
        "-ss", "00:01",
        "-pix_fmt", "yuv420p",
        "-c:v", "libopenjpeg"
    ]);

//echo(join(' ', $ffmpeg->buildCommand()));

$ffmpeg->progress(function ($progress) {
    echo($progress.'%'.PHP_EOL);
    if ($progress > 50) {
        //throw new CancelProcessException();
    }
});

$ffmpeg->run();