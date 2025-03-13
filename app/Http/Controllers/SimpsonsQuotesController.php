<?php

namespace App\Http\Controllers;

use App\SimpsonsQuotes\SimpsonsQuotes;

class SimpsonsQuotesController
{
    protected SimpsonsQuotes $simpsonsQuotes;

    public function __construct(SimpsonsQuotes $simpsonsQuotes)
    {
        $this->simpsonsQuotes = $simpsonsQuotes;
    }

    public function show(): array
    {
        return $this->simpsonsQuotes->composeQuotesForDisplay();
    }
}
