<?php

namespace App\Dto;

final class TraduzioneInput
{
    public function __construct(
        public string $testo = '',
        public ?string $info = null
    ) {}
}
