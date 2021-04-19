<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace DefStudio\ModelDetails\Traits;


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

    public function set_detail(string $key, mixed $value): static
    {
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
}
