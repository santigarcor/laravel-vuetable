<?php

namespace Tests\Unit;

use Vuetable\Vuetable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vuetable\Tests\VuetableTestCase;
use Vuetable\Builders\CollectionVuetableBuilder;

class VuetableCollectionTest extends VuetableTestCase
{
    /**
     * @var Collection
     */
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->collection = new Collection(
            [
                ['name' => 'John Doe', 'email' => 'john@mail.com'],
                ['name' => 'Jane Doe', 'email' => 'jane@mail.com'],
                ['name' => 'Test John', 'email' => 'test@mail.com']
            ]
        );
    }

    /** @test */
    public function it_returns_the_eloquent_vuetable_builder()
    {
        $this->assertInstanceOf(
            CollectionVuetableBuilder::class,
            Vuetable::of($this->collection)
        );

        $this->assertInstanceOf(
            CollectionVuetableBuilder::class,
            (new Vuetable($this->app['request']))->collection($this->collection)
        );
    }

    /** @test */
    public function it_throws_an_exception_if_not_passed_an_eloquent_builder()
    {
        $this->expectException('Exception', 'Unsupported builder type');
        Vuetable::of(['test']);
    }

    /** @test */
    public function it_paginates_the_result()
    {
        $result = Vuetable::of($this->collection)->make();

        $this->assertArrayHasKey('current_page', $result->toArray());
        $this->assertArrayHasKey('data', $result->toArray());
    }

    /** @test */
    public function it_paginates_depending_on_the_request_per_page()
    {
        $result = $this->createRequestAndReturnVuetable([
            'per_page' => 2
        ]);

        $this->assertEquals(2, $result->perPage());
    }

    /** @test */
    public function it_paginates_to_default_if_no_per_page_given()
    {
        $result = $this->createRequestAndReturnVuetable();

        $this->assertEquals(15, $result->perPage());
    }

    /** @test */
    public function it_sorts_the_collection_by_the_request_sort_asc()
    {
        $result = $this->createRequestAndReturnVuetable([
            'sort' => 'name|asc'
        ]);

        $user = $this->collection->get(1);

        $this->assertEquals($user['name'], $result->getCollection()->first()['name']);
    }

    /** @test */
    public function it_sorts_the_collection_by_the_request_sort_desc()
    {
        $result = $this->createRequestAndReturnVuetable([
            'sort' => 'name|desc'
        ]);

        $user = $this->collection->get(2);

        $this->assertEquals($user['name'], $result->getCollection()->first()['name']);
    }

    /** @test */
    public function it_filters_the_query_by_the_searchable_columns()
    {
        $result = $this->createRequestAndReturnVuetable([
            'filter' => 'Doe',
            'searchable' => ['name', 'email']
        ]);

        $this->assertCount(2, $result->getCollection());
    }


    /** @test */
    public function it_supports_editing_columns_with_string()
    {
        $result = Vuetable::of($this->collection)
            ->editColumn('name', 'new_test_name')
            ->make();

        $this->assertEquals('new_test_name', $result->getCollection()->first()['name']);
    }

    /** @test */
    public function it_supports_editing_columns_with_callback()
    {
        $result = Vuetable::of($this->collection)
            ->editColumn('name', function () {
                return 'new_test_name';
            })
            ->make();

        $this->assertEquals('new_test_name', $result->getCollection()->first()['name']);
    }

    /** @test */
    public function it_supports_adding_new_colums()
    {
        $result = Vuetable::of($this->collection)
            ->addColumn('action', function () {
                return 'some action';
            })
            ->make();

        $this->assertEquals('some action', $result->getCollection()->first()['action']);
    }

    /** @test */
    public function it_throws_an_exception_when_adding_a_column_if_it_already_exists()
    {
        try {
            Vuetable::of($this->collection)
                ->addColumn('name', function () {
                    return 'some action';
                })
                ->make();
        } catch (\Exception $e) {
            $this->assertEquals(
                "Can not add the 'name' column, the results already have that column.",
                $e->getMessage()
            );
        }
    }

    public function createRequestAndReturnVuetable($requestData = [])
    {
        $this->app['request'] = Request::create('http://url.com', 'GET', $requestData);

        return Vuetable::of($this->collection)->make();
    }
}
