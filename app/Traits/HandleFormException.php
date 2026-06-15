<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

trait HandleFormException
{
    protected function handleException(\Exception $e): never
    {
        $pesan = match(true) {
            $e instanceof ValidationException                  => null,
            $e instanceof ModelNotFoundException               => 'Data tidak ditemukan',
            $e instanceof UniqueConstraintViolationException   => 'Data sudah ada',
            $e instanceof QueryException => match($e->errorInfo[1] ?? null) {
                1451, 1452 => 'Data berelasi tidak valid',
                1048       => 'Ada kolom yang tidak boleh kosong',
                1213, 1205 => 'Konflik data, coba lagi',
                default    => 'Error database',
            },
            default => 'Terjadi kesalahan sistem',
        };

        if ($e instanceof ValidationException) {
            throw $e;
        }

        Notification::make()
            ->title($pesan)
            ->danger()
            ->send();

        $this->halt();
        throw $e;
    }

    protected function runInTransaction(callable $callback): mixed
    {
        try {
            return \DB::transaction($callback);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
}