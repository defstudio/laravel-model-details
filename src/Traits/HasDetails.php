<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

namespace DefStudio\ModelDetails\Traits;


use Illuminate\Support\Arr;

trait HasDetails
{
    public function set_details(array $details, bool $merge = false): static
    {
        if ($merge) {
            $old_details = $this->getAttribute('details') ?? [];
            $details = array_replace_recursive($old_details, $details);
        }

        /** @noinspection PhpUndefinedClassInspection */
        parent::setAttribute('details', $details);

        return $this;
    }

    public function set_detail(string $key, mixed $value): static
    {
        $details = $this->details;
        data_set($details, $key, $value);

        /** @noinspection PhpUndefinedClassInspection */
        parent::setAttribute('details', $details);

        return $this;
    }

    public function get_detail(string|array $key, mixed $default = null): mixed
    {
        return data_get($this->getAttribute('details'), $key, $default);
    }

    public function forget_detail(string $key): static
    {
        $details = $this->details;
        Arr::forget($details, $key);

        /** @noinspection PhpUndefinedClassInspection */
        parent::setAttribute('details', $details);

        return $this;
    }
}
