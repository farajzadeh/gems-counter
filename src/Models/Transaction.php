<?php

namespace Farajzadeh\GemsCounter\Models;

use Illuminate\Database\Eloquent\Model;
use Farajzadeh\GemsCounter\Contracts\Transaction as TransactionContract;

class Transaction extends Model implements TransactionContract
{
    protected $fillable = ['change_amount', 'tag', 'last_count'];

    public function getTable()
    {
        return config('gems-counter.table_names.transactions', parent::getTable());
    }

}