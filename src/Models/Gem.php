<?php

namespace Farajzadeh\GemsCounter\Models;

use Illuminate\Database\Eloquent\Model;
use Farajzadeh\GemsCounter\Contracts\Gem as GemContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gem extends Model implements GemContract
{
    protected $fillable = ['gems_count'];

    public function getTable()
    {
        return config('gems-counter.table_names.gem', parent::getTable());
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            config('gems-counter.models.transaction'),
            config('gems-counter.column_names.user_foreign_key')
        );
    }
}