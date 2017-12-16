<?php

namespace Vuetable\Builders;

abstract class BaseBuilder
{
    /**
     * The current request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;


    /**
     * Array of columns that should be edited and the new content.
     *
     * @var array
     */
    protected $columnsToEdit = [];

    /**
     * Array of columns that should be added and the new content.
     *
     * @var array
     */
    protected $columnsToAdd = [];


    /**
     * Add a new column to edit with its new value.
     *
     * @param  string $column
     * @param  string|\Closure $content
     * @return $this
     */
    public function editColumn($column, $content)
    {
        $this->columnsToEdit[$column] = $content;

        return $this;
    }

    /**
     * Add a new column to the columns to add.
     *
     * @param string $column
     * @param string|\Closure $content
     */
    public function addColumn($column, $content)
    {
        $this->columnsToAdd[$column] = $content;

        return $this;
    }
}
