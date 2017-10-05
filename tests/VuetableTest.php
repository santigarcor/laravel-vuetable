<?php

namespace Tests\Unit;

use Vuetable\Vuetable;
use Illuminate\Http\Request;
use Vuetable\Tests\Models\User;
use Illuminate\Support\Facades\DB;
use Vuetable\Tests\VuetableTestCase;

class VuetableTest extends VuetableTestCase
{
    private $query;

    public function setUp()
    {
        parent::setUp();
        $this->migrate();
        $this->seed(\Vuetable\Tests\Migrations\TestSeeder::class);

        $this->query = User::query();
    }

    /** @test */
    public function it_returns_the_eloquent_vuetable_builder()
    {
        $this->assertInstanceOf(
            'Vuetable\Builders\EloquentVuetableBuilder',
            Vuetable::of($this->query)
        );

        $this->assertInstanceOf(
            'Vuetable\Builders\EloquentVuetableBuilder',
            (new Vuetable($this->app['request']))->eloquent($this->query)
        );
    }

    /** @test */
    public function it_throws_an_exception_if_not_passed_an_eloquent_builder()
    {
        $this->expectException('Exception', 'Unsupported builder type');
        Vuetable::of(DB::table('users')->select('name'));
    }

    /** @test */
    public function it_paginates_the_result()
    {
        $result = Vuetable::of($this->query)->make();

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
    public function it_sorts_the_query_by_the_request_sort_asc()
    {
        $result = $this->createRequestAndReturnVuetable([
            'sort' => 'name|asc'
        ]);
        $user = User::orderBy('name', 'asc')->first();

        $this->assertEquals($user->name, $result->getCollection()->first()->name);
    }

    /** @test */
    public function it_sorts_the_query_by_the_request_sort_desc()
    {
        $result = $this->createRequestAndReturnVuetable([
            'sort' => 'name|desc'
        ]);
        $user = User::orderBy('name', 'desc')->first();

        $this->assertEquals($user->name, $result->getCollection()->first()->name);
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
    public function it_supports_sorting_by_nested_relationships()
    {
        $this->query = User::select('users.name', 'users.email', 'cars.name as car')
            ->join('cars', 'cars.user_id', '=', 'users.id');

        $result = $this->createRequestAndReturnVuetable([
            'sort' => 'cars.name|desc',
        ]);

        $user = User::select('users.name', 'users.email', 'cars.name as car')
            ->join('cars', 'cars.user_id', '=', 'users.id')
            ->orderBy('cars.name', 'desc')
            ->first();

        $this->assertEquals($user->name, $result->getCollection()->first()->name);
    }

    /** @test */
    public function it_supports_editing_columns_with_string()
    {
        $result = Vuetable::of($this->query)
            ->editColumn('name', 'new_test_name')
            ->make();

        $this->assertEquals('new_test_name', $result->getCollection()->first()->toArray()['name']);
    }

    /** @test */
    public function it_supports_editing_columns_with_callback()
    {
        $result = Vuetable::of($this->query)
            ->editColumn('name', function ($model) {
                return 'new_test_name';
            })
            ->make();

        $this->assertEquals('new_test_name', $result->getCollection()->first()->toArray()['name']);
    }

    /** @test */
    public function it_throws_an_exception_if_the_column_to_edit_has_a_cast()
    {
        $this->expectExceptionMessage('Can not edit the \'is_admin\' attribute, it has a cast defined in the model.');
        $this->expectException(\Exception::class);

        Vuetable::of($this->query)
            ->editColumn('is_admin', function ($model) {
                return $model->is_admin ? 'Admin' : 'Not Admin';
            })
            ->make();
    }

    /** @test */
    public function it_supports_editing_relatinship_columns_with_callback()
    {
        $result = Vuetable::of($this->query->with(['roles', 'cars']))
            ->editColumn('cars', function ($model) {
                return 'new_cars_relation_content';
            })
            ->editColumn('roles', function ($model) {
                return 'new_roles_relation_content';
            })
            ->make();

        $this->assertEquals('new_cars_relation_content', $result->getCollection()->first()->toArray()['cars']);
        $this->assertEquals('new_roles_relation_content', $result->getCollection()->first()->toArray()['roles']);
    }

    /** @test */
    public function it_supports_adding_new_colums()
    {
        $result = Vuetable::of($this->query)
            ->addColumn('action', function ($model) {
                return 'some action';
            })
            ->make();

        $this->assertEquals('some action', $result->getCollection()->first()->toArray()['action']);
    }

    /** @test */
    public function it_throws_an_exception_when_adding_a_column_if_it_already_exists()
    {
        try {
            Vuetable::of($this->query)
                ->addColumn('name', function ($model) {
                    return 'some action';
                })
                ->make();
        } catch (\Exception $e) {
            $this->assertEquals(
                "Can not add the 'name' column, the results already have that column.",
                $e->getMessage()
            );
        }

        try {
            Vuetable::of(User::with('roles'))
                ->addColumn('roles', function ($model) {
                    return 'some action';
                })
                ->make();
        } catch (\Exception $e) {
            $this->assertEquals(
                "Can not add the 'roles' column, the results already have that column.",
                $e->getMessage()
            );
        }
    }

    public function createRequestAndReturnVuetable($requestData = [])
    {
        $this->app['request'] = Request::create('http://url.com', 'GET', $requestData);

        return Vuetable::of($this->query)->make();
    }
}
