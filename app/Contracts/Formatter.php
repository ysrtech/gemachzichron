<?php

namespace App\Contracts;

interface Formatter
{
    public function formatOutput($output): array;
}
