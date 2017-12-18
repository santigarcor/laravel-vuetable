<?php

namespace Vuetable;

use Illuminate\Http\Request;
use Vuetable\Builders\CollectionVuetableBuilder;
use Vuetable\Builders\EloquentVuetableBuilder;

class Vuetable
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle automatic builder
     *
     * @param  mixed $source
     * @return CollectionVuetableBuilder|EloquentVuetableBuilder
     * @throws \Exception
     */
    public static function of($source)
    {
        $request = app('request');

//        if (get_class($query) != \Illuminate\Database\Eloquent\Builder::class) {
//            throw new \Exception('Unsupported builder type');
//        }

        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return new EloquentVuetableBuilder($request, $source);
        } elseif ($source instanceof \Illuminate\Support\Collection) {
            return new CollectionVuetableBuilder($request, $source);
        } else {
            throw new \Exception('Unsupported builder type: '.gettype($source));
        }
    }

    /**
     * Return the Eloquent Vuetable Builder
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Vuetable\Builders\EloquentVuetableBuilder
     */
    public function eloquent($query)
    {
        return new EloquentVuetableBuilder($this->request, $query);
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     */
    public function collection($collection)
    {
        return new CollectionVuetableBuilder($this->request, $collection);
    }

    public function getRequest()
    {
        return $this->request;
    }
}
