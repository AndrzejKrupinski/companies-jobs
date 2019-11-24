<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Repository
{
    /** @var string */
    protected $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    public function all(): array
    {
        return $this->model::all()->all();
    }

    public function find(int $id): Model
    {
        return $this->model::find($id);
    }

    public function save(array $attributes): Model
    {
        return $this->model::create($attributes);
    }

    public function update(array $attributes, int $id): bool
    {
        return $this->find($id)->update($attributes);
    }

    public function delete(int $id): ?bool
    {
        return $this->find($id)->delete();
    }
}
