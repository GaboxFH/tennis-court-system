---
id: rolesAndPermissions
title: Roles and Permissions
---

Laravel-permission (https://spatie.be/docs/laravel-permission/v3/introduction) by Spatie allows you to manage user permissions and roles in a database.

## Installation

```
 composer require spatie/laravel-permission
 ```

Clear your config cache

```
 php artisan config:clear
 ```
 
## Setting it up
 
### Publish the migrations
 
 ```
 php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
 ```
 
### Create database tables

Create the tables for this package by running:

```
php artisan migrate
```

### Updating User Model

 First, add the Spatie\Permission\Traits\HasRoles trait to your User model(s):
 
:::caution

Dont overwrite other traits, just make sure you are adding to the existing model.

:::
 
```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```
 
  
 ### Updating the middleware
 
 Spatie package provide it's in-built middleware which can be added in Kernel.php file this way :

```app/Http/Kernel.php```

```php
....
protected $routeMiddleware = [
	....
	'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
	'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
]
....
```
 
### Create Permissions and Roles

Now is the time to decide what kind of permissions and roles our application will have. 


#### Create artisan command

```php
php artisan make:command CreateRolesPermissions
```

Add the following code to ```app/Console/Commands/CreateRolesPermissions.php```

 ```php
<?php
 
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel_api:bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles and permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
 public function handle()
    {

        $roles = ["Super Admin", "User Manager","Role Manager"];

        $permissions = [
            "View All Users", 
            "Edit All Users", 
            "Assign Role", 
            "Unassign Role", 
            "View All Permissions",
            "View All Roles"];


        $this->line('------------- Setting Up Roles:');

        foreach ($roles as $role) {
            $role = Role::updateOrCreate(['name' => $role, 'guard_name' => 'api']);
            $this->info("Created " . $role->name . " Role");
        }

        $this->line('------------- Setting Up Permissions:');

        $superAdminRole = Role::where('name', "Super Admin")->first();

        foreach ($permissions as $perm_name) {
            $permission = Permission::updateOrCreate(['name' => $perm_name,
                'guard_name' => 'api']);

            $superAdminRole->givePermissionTo($permission);

            $this->info("Created " . $permission->name . " Permission");
        }

        $this->info("All permissions are granted to Super Admin");
        $this->line('------------- Application Bootstrapping is Complete: \n');
    }
}
```

#### Run the Artisan command

Run ```rolespermissions:create``` to generate roles and permissions and persist them into the database.

```php
php artisan rolespermissions:create
```

### Laravel Permissions in Vue Components

 #### Add accessor to User model that returns permissions
 
 Add an accessor to your User model that returns an array of permission names that the user has.

In app\Models\User.php:

```php

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

public function getAllPermissionsAttribute() {
  $permissions = [];
    foreach (Permission::all() as $permission) {
      if (Auth::user()->can($permission->name)) {
        $permissions[] = $permission->name;
      }
    }
    return $permissions;
}
```

#### Add a global javascript array of the user’s permissions

In a file that is rendered on every page, such as ```resources/views/spa.blade.php```, globally define the permissions as a JavaScript array.

 ```javascript
 <script>
  @auth
    window.Permissions = {!! json_encode(Auth::user()->allPermissions, true) !!};
  @else
    window.Permissions = [];
  @endauth
</script>
```

####  Create a global mixins file with a method to check if a user has a permission

Mixins are a great way to share functionality across components. In this case, we’ll share a method named $can globally, across all components. It accesses the Permissions array that we set in the layout file.

Create a ```resources/js/mixins/Permissions.vue``` file. Laravel does not ship with a mixins directory by default, so create the directory if you need to.

The new file should contain:

#### Import the mixin globally

Modify ```resources/js/app.js``` to import the mixin

```php
import Permissions from './mixins/Permissions';
Vue.mixin(Permissions);
```

#### Rebuild assets

If you’re not watching your assets for changes.

```
npm run dev
```

#### Check permissions in Vue components as needed

You can now use $can in v-if conditions in templates used by Vue components.

For example, this would only show “You can edit posts.” if a user had the “edit posts” permission:

```
<div v-if="$can('edit posts')">You can edit posts.</div>
```

You can also access $can in the methods of Vue components.

Remember not to rely on the front-end alone, and always check permissions on the backend as well.


### Additional comments

#### Change guard_name of user model

:::note

Seems like the default configuration of spatie uses api guards. Need to look more into this to fully understand what this change

:::

```php
//Added this to make guard used by spatie be api instead of web.
    protected $guard_name = 'api';
```
 ## Basic Usage
 
 Eventually, this functionality will be added to a user interface. In the mean time, you can test it with Laravel Tinker (https://laravel.com/docs/8.x/artisan#tinker). This is an REPL, or an interactive shell that you can run commands and interact with your application.
 
 ```php
 php artisan tinker
Psy Shell v0.10.5 (PHP 7.4.11 — cli) by Justin Hileman
>>>
```

You can do queries for a model 

```php
User::find(1)
```

Return the User with an id of 1

```php

[!] Aliasing 'User' to 'App\Models\User' for this Tinker session.
=> App\Models\User {#4211
     id: 1,
     name: "Gabe",
     email: "gabefernandez@ufl.edu",
     email_verified_at: null,
     created_at: "2021-01-27 06:41:10",
     updated_at: "2021-01-27 06:41:10",
```
 
 
For using laravel-permitions, you can use built-in commands

```
User::find(1)->getRoleNames()
```
Returns a json with all roles for that user

```php
=> Illuminate\Support\Collection {#4373
     all: [
       "Super Admin",
     ],
   }
   ```

Assigning a role to a user

```php
User::find(1)->assignRole("Super Admin")
```

```
=> App\Models\User {#4211
     id: 1,
     name: "Gabe",
     email: "gabefernandez@ufl.edu",
     email_verified_at: null,
     created_at: "2021-01-27 06:41:10",
     updated_at: "2021-01-27 06:41:10",
     roles: Illuminate\Database\Eloquent\Collection {#4366
       all: [
         Spatie\Permission\Models\Role {#4359
           id: 1,
           name: "Super Admin",
           guard_name: "api",
           created_at: "2021-01-27 07:24:03",
           updated_at: "2021-01-27 07:24:03",
           pivot: Illuminate\Database\Eloquent\Relations\MorphPivot {#4365
             model_id: 1,
             role_id: 1,
             model_type: "App\Models\User",
           },
         },
       ],
     },
   }
   ```
   
   Displaying permissions (inherited and direct)
   
   ```php
    User::find(1)->getAllPermissions()
	```
	
	Removing a role
	
	```php
	User::find(1)->removeRole("Super Admin")
	```
	
	```php 
	=> App\Models\User {#4211
     id: 1,
     name: "Gabe",
     email: "gabefernandez@ufl.edu",
     email_verified_at: null,
     created_at: "2021-01-27 06:41:10",
     updated_at: "2021-01-27 06:41:10",
     roles: Illuminate\Database\Eloquent\Collection {#265
       all: [],
     },
   }
   ```
   
 For a full list of methonds, check the Spatie documentation: 
 https://spatie.be/docs/laravel-permission/v3/basic-usage/basic-usage
 https://spatie.be/docs/laravel-permission/v3/basic-usage/role-permissions
	
	

References: 
https://mmccaff.github.io/2018/11/03/laravel-permissions-in-vue-components/