<?php

namespace App\FeedImporters;

interface ImporterContract
{
    public function import(): void;
}
