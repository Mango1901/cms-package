<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Role::insert([
           [
           "name"=>"Admin",
            "guard_name"=>"web",
            "created_at"=>date("Y-m-d H:i:s"),
            "updated_at"=>date("Y-m-d H:i:s")
           ],
            [
                "name"=>"User",
                "guard_name"=>"web",
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ],
            [
                "name"=>"Author",
                "guard_name"=>"web",
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ]
        ]);
        Permission::insert([
           [
               "name"=>"All Privileges",
               "guard_name"=>"web",
               "created_at"=>date("Y-m-d H:i:s"),
               "updated_at"=>date("Y-m-d H:i:s")
           ],
            [
                "name"=>"show",
                "guard_name"=>"web",
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ],
            [
                "name"=>"edit",
                "guard_name"=>"web",
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ]

        ]);
        \DB::table("model_has_roles")->insert([
           [
               "role_id"=>"1",
               "model_type"=>"App\Models\User",
               "model_id"=>"1"
           ],
            [
                "role_id"=>"2",
                "model_type"=>"App\Models\User",
                "model_id"=>"2"
            ],
            [
                "role_id"=>"3",
                "model_type"=>"App\Models\User",
                "model_id"=>"3"
            ],
        ]);
        User::insert([
           [
             "name"=>"Nguyễn Thị Huyền",
             "email"=>"hieu.tuhai2001@gmail.com",
             "password"=>bcrypt("huyenbon"),
               "created_at"=>date("Y-m-d H:i:s"),
               "updated_at"=>date("Y-m-d H:i:s")
           ],
            [
                "name"=>"Từ Hải Hiếu",
                "email"=>"huyenbon99@gmail.com",
                "password"=>bcrypt("huyenbon"),
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ],
            [
                "name"=>"Jkai",
                "email"=>"deagleka1@gmail.com",
                "password"=>bcrypt("huyenbon"),
                "created_at"=>date("Y-m-d H:i:s"),
                "updated_at"=>date("Y-m-d H:i:s")
            ]
        ]);
        \DB::table("role_has_permissions")->insert([
            ["permission_id"=>1,
            "role_id"=>1
            ],
            ["permission_id"=>2,
                "role_id"=>2
            ],
            ["permission_id"=>2,
                "role_id"=>3
            ],
            ["permission_id"=>3,
                "role_id"=>3
            ],
            ]);
        \DB::table("formats")->insert([
            ["name"=>"Standard"],
            ["name"=>"Aside"],
            ["name"=>"Image"],
            ["name"=>"Video"],
            ["name"=>"Audio"],
            ["name"=>"Quote"],
            ["name"=>"Link"],
            ["name"=>"Gallery"]
        ]);
    }
}
