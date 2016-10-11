<?php

namespace DamianTW\MySQLScout\Engines\Modes;

use Laravel\Scout\Builder;
use DamianTW\MySQLScout\Services\ModelService;

class NaturalLanguage extends Mode
{
    protected $modelService;

    function __construct(Builder $builder)
    {
        parent::__construct($builder);

        $this->modelService = resolve(ModelService::class);
        $this->modelService->setModel($this->builder->model);
    }

    public function buildWhereRawString()
    {
        $queryString = '';

        $queryString .= $this->buildWheres();

        $indexFields = implode(',',  $this->modelService->getFullTextIndexFields());

        $queryString .= "MATCH($indexFields) AGAINST(:_search IN NATURAL LANGUAGE MODE";

        if(config('scout.mysql.query_expansion')) {
            $queryString .= ' WITH QUERY EXPANSION';
        }

        $queryString .= ')';

        return $queryString;

    }

    public function buildParams()
    {

        $this->whereParams['_search'] = $this->builder->query;
        return $this->whereParams;
    }

    public function isFullText()
    {
        return true;
    }
}