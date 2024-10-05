<?php

namespace App\Traits;

trait ToggleSectionLivewire
{
    public function toggle(&$property)
    {
        $property = !$property;
    }
}
