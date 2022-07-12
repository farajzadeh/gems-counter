<?php

namespace Farajzadeh\GemsCounter\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Gem
{
    public function transaction(): BelongsTo;
}