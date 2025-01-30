<?php

namespace Rudi97277\ExportDb\DTOs;


class ExportDTO
{
    public $styles;
    public $registerEvents;

    public function __construct(
        ?callable $styles = null,
        ?callable $registerEvents = null
    ) {
        $this->styles = $styles;
        $this->registerEvents = $registerEvents;
    }
}
