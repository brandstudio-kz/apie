<?php

namespace BrandStudio\Apie;

use BrandStudio\Apie\Traits\Operations\Get;
use BrandStudio\Apie\Traits\Operations\Insert;
use BrandStudio\Apie\Traits\Operations\Update;
use BrandStudio\Apie\Traits\Operations\Delete;

class Query
{
    use Get, Insert, Update, Delete;

    protected $class;
    protected $query;

    public function __construct(string $class)
    {
        $this->class = $class;
        $this->query = $class::query();
    }

    public function select($data) : self
    {
        static::applyGet($this->query, $data);

        return $this;
    }

    public function insert($data)
    {
        return static::applyInsert($this->class, $data);
    }

    public function update($data) : self
    {
        return static::applyUpdate($this->query, $data);

        return $this;
    }

    public function delete($data) : self
    {
        static::applyDelete($this->query, $data);

        return $this;
    }

    public function search(string $query)
    {
        return $this->class::search($query);
    }

    public function get(array $pagination)
    {
        if (isset($pagination['count'])) {
            return $this->query->count();
        }
        if (isset($pagination['per_page'])) {
            if ($pagination['per_page'] == 1) {
                return $this->query->first();
            } else {
                $page = $pagination['page'] ?? 1;
                return $this->query->paginate($pagination['per_page']);
            }
        } else {
            return $this->query->get();
        }
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->query, $method], $arguments);
    }

}
