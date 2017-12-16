# Laravel Vuetable (Laravel 5.4 Package)

[![Build Status](https://travis-ci.org/santigarcor/laravel-vuetable.svg?branch=master)](https://travis-ci.org/santigarcor/laravel-vuetable)
[![Latest Stable Version](https://poser.pugx.org/santigarcor/laravel-vuetable/v/stable)](https://packagist.org/packages/santigarcor/laravel-vuetable)
[![Total Downloads](https://poser.pugx.org/santigarcor/laravel-vuetable/downloads)](https://packagist.org/packages/santigarcor/laravel-vuetable)
[![StyleCI](https://styleci.io/repos/99027423/shield?branch=master)](https://styleci.io/repos/99027423)
[![License](https://poser.pugx.org/santigarcor/laravel-vuetable/license)](https://packagist.org/packages/santigarcor/laravel-vuetable)

Laravel Vuetable is the backend component that can work with the [Vuetable component](https://github.com/ratiw/vuetable-2).

## Installation
1. Run the composer require command from your terminal:

        composer require "santigarcor/laravel-vuetable:0.0.*"

2. If you have disabled the package discovery in your `config/app.php`:
    - In the providers array add:

            Vuetable\VuetableServiceProvider::class,

    - In the aliases array add:

            'Vuetable' => Vuetable\VuetableFacade::class,

## Usage
Your request to the controller should have this data:

```javascript
{
    sort: '', // column_name|asc or column_name|desc
    page: 1,
    per_page: 10,
    searchable: [
        // This array should have the names of the columns in the database
    ],
    filter: '' //The text that is going to be used to filter the data
}
```

So for example lets create the table for the users with their companies. Then in the javascript we should have:

```javascript
data = {
    sort: 'users.name|asc',
    page: 1,
    per_page: 10,
    searchable: [ // This means the 'users.name', 'users.email' and 'companies.name' columns can be filtered through the 'filter' attribute in the data.
        'users.name',
        'users.email',
        'companies.name',
    ]
}

axios.get('http://url.com/users-with-companies', data)
```

In Controller we can provide Eloquent:

```php
class UsersDataController extends Controller
{
    public function index() {

        $query = User::select([
                'users.id',
                'users.name',
                'users.email',
                'companies.name as company',
                'companies.company_id'
            ])
            ->leftJoin('companies', 'users.company_id', '=', 'companies.id');

        return Vuetable::of($query)
            ->editColumn('company', function ($user) {
                if ($user->company) {
                    return $user->company;
                }

                return '-';
            })
            ->addColumn('urls', function ($user) {
                return [
                    'edit' => route('users.edit', $user->id),
                    'delete' => route('users.destroy', $user->id),
                ];
            })
            ->make();
    }
}
```

Or Collection
```php
class UsersDataController extends Controller
{
    public function index() {

        $query = new Collection([
             ['name' => 'John Doe', 'email' => 'john@mail.com'],
             ['name' => 'Jane Doe', 'email' => 'jane@mail.com'],
             ['name' => 'Test John', 'email' => 'test@mail.com']
        ]);

        return Vuetable::of($query)
            ->editColumn('name', function ($user) {
                return Str::lower($user['name']);
            })
            ->addColumn('urls', function ($user) {
                return [
                    'edit' => route('users.edit', $user['id']),
                    'delete' => route('users.destroy', $user['id']),
                ];
            })
            ->make();
    }
}
```
This controller is going to return:
```json
{
  "current_page": 1,
  "from": 1,
  "to": 10,
  "total": 150,
  "per_page": 10,
  "last_page": 15,
  "first_page_url": "http://url.com/users-with-companies?page=1",
  "last_page_url": "http://url.com/users-with-companies?page=15",
  "next_page_url": "http://url.com/users-with-companies?page=2",
  "prev_page_url": null,
  "path": "http://url.com/users-with-companies",
  "data": [
    {
      "id": 1,
      "name": "Administrator",
      "email": "administrator@app.com",
      "company": "-",
      "company_id": null,
      "urls": {
        "edit": "http://url.com//users/1/edit",
        "delete": "http://url.com//users/1"
      },
    },
    {
      "id": 2,
      "name": "Company Administrator",
      "email": "company_administrator@app.com",
      "company": "-",
      "company_id": null,
      "urls": {
        "edit": "http://url.com//users/2/edit",
        "delete": "http://url.com//users/2"
      },
      ...
    }
  ],
}
```

## What does Laravel Vuetable support?

Using the Eloquent Builder you can:
- Filter/Sort by model columns.
- Make joins and filter/sort by them.
- Define the length of the pagination.
- Add columns.
- Edit columns (if the column has a cast defined, it doesn't work).

Using the Collection you can:
- Filter/Sort by model columns.
- Define the length of the pagination.
- Add columns.
- Edit columns.

## License

Laravel Vuetable is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Contributing

Please report any issue you find in the issues page. Pull requests are more than welcome.
