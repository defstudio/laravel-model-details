<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace DefStudio\ModelDetails\Traits;


use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasDetails
{
    public static function bootHasDetails()
    {
        self::creating(function (HasDetails|Model $model) {
            if (empty($model->details)) {
                $model->details = [];
            }
        });
    }

    public function set_details(array $details, bool $merge = false): static
    {
        if ($merge) {
            $old_details = $this->getAttribute('details') ?? [];
            $details = array_replace_recursive($old_details, $details);
        }

        parent::setAttribute('details', $details);

        return $this;
    }

    public function append_detail(string $key, mixed $value): static
    {
        $array = $this->get_detail($key);

        if (empty($array)) {
            $array = [];
        }

        if (!is_array($array)) {
            throw new Exception("Trying to append a detail to a non array key");
        }

        $array[] = $value;

        $this->set_detail($key, $array);

        return $this;
    }

    public function set_detail(string $key, mixed $value, bool $overwrite = true): static
    {
        if (!empty($this->get_detail($key)) && !$overwrite) {
            return $this;
        }

        $details = $this->details;
        data_set($details, $key, $value);

        parent::setAttribute('details', $details);

        return $this;
    }

    public function get_detail(string|array $key, mixed $default = null): mixed
    {
        return data_get($this->getAttribute('details'), $key, $default);
    }

    public function has_detail(string|array $key): bool
    {
        $dummy_value = Str::uuid();
        return !(data_get($this->getAttribute('details'), $key, $dummy_value) === $dummy_value);
    }

    public function forget_detail(string $key): static
    {
        $details = $this->details;
        Arr::forget($details, $key);

        parent::setAttribute('details', $details);

        return $this;
    }

    public function jsonDetails(): Attribute
    {
        return Attribute::make(
            get: fn () => json_encode(json_decode($this->attributes['details'] ?? '{}'), JSON_PRETTY_PRINT),
            set: fn (string $value) => ['details' => json_encode(json_decode($value,JSON_PRETTY_PRINT))],
        );
    }
}
