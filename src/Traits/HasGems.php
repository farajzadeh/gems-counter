<?php

namespace Farajzadeh\GemsCounter\Traits;

use Farajzadeh\GemsCounter\Contracts\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

trait HasGems
{
    public static function bootHasGems()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->gem()->delete();
            $model->transactions()->delete();
        });
    }

    public function gem(): HasOne
    {
        return $this->hasOne(
            config('gems-counter.models.gem'),
            config('gems-counter.column_names.user_foreign_key')
        );
    }

    public function getGemsCountAttribute()
    {
        return $this->getModel()->gem ? $this->getModel()->gem->gems_count : 0;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(
            config('gems-counter.models.transaction'),
            config('gems-counter.column_names.user_foreign_key')
        );
    }

    private function updateOrCreateGem($gems_count, $transaction_id) {
        $gemModel = config('gems-counter.models.gem');
        $model = $this->getModel();
        if($model->gem) {
            $model->gem->gems_count = $gems_count;
            $model->gem->{config('gems-counter.column_names.transaction_foreign_key')} = $transaction_id;
            $model->gem->save();
        } else {
            $gem = new $gemModel(['gems_count' => $gems_count]);
            $gem->{config('gems-counter.column_names.transaction_foreign_key')} = $transaction_id;
            $this->gem()->save($gem);
        }
    }

    public function createTransaction($amount, $tag): Transaction
    {
        DB::beginTransaction();
        try {
            $model = $this->getModel();
            $transactionModel = config('gems-counter.models.transaction');
            $lastScore = $model->gem ? $model->gem->gems_count : 0;
            $transaction = new $transactionModel(['change_amount' => $amount, 'tag' => $tag, 'last_count' => $lastScore]);
            $model->transactions()->save($transaction);
            $this->updateOrCreateGem($amount + $lastScore, $transaction->id);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return $transaction;
    }
}