<?php

use FFMpeg\Timecode;

it('can be constructed', function () {
    $timecode = new Timecode(1, 2, 3, 4);
    expect($timecode->hours)->toEqual(1);
    expect($timecode->minutes)->toEqual(2);
    expect($timecode->seconds)->toEqual(3);
    expect($timecode->frames)->toEqual(4);
});

it('can be constructed from seconds', function () {
    $timecode = Timecode::fromSeconds(130);
    expect($timecode->hours)->toEqual(0);
    expect($timecode->minutes)->toEqual(2);
    expect($timecode->seconds)->toEqual(10);
    expect($timecode->frames)->toEqual(0);
});

it('can be constructed from seconds with frames', function () {
    $timecode = Timecode::fromSeconds(40.12);
    expect($timecode->hours)->toEqual(0);
    expect($timecode->minutes)->toEqual(0);
    expect($timecode->seconds)->toEqual(40);
    expect($timecode->frames)->toEqual(12);
});

it('can be converted to seconds', function () {
    $timecode = new Timecode(0, 20, 10, 0);
    expect($timecode->toSeconds())->toBe(1210);
});

it('can be converted to string', function () {
    $timecode = new Timecode(0, 20, 10, 0);
    expect((string) $timecode)->toBe('00:20:10.00');
});

it('can parse a timecode string', function () {
    $timecode = Timecode::parse('02:40:30');
    expect($timecode->hours)->toEqual(2);
    expect($timecode->minutes)->toEqual(40);
    expect($timecode->seconds)->toEqual(30);
    expect($timecode->frames)->toEqual(0);
});

it('can parse a partial timecode string', function () {
    $timecode = Timecode::parse('40:30');
    expect($timecode->hours)->toEqual(0);
    expect($timecode->minutes)->toEqual(40);
    expect($timecode->seconds)->toEqual(30);
    expect($timecode->frames)->toEqual(0);
});

it('can parse a timecode string including frames', function () {
    $timecode = Timecode::parse('40:30.12');
    expect($timecode->hours)->toEqual(0);
    expect($timecode->minutes)->toEqual(40);
    expect($timecode->seconds)->toEqual(30);
    expect($timecode->frames)->toEqual(12);
});
