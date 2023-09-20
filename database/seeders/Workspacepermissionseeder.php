<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkspacePermission;

class Workspacepermissionseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'role' => 'Owner',
                'permission' => json_encode([

                    "create task",
                    "edit task",
                    "delete task",
                    "show task",
                    "move task",
                    "show activity",
                    "show uploading",
                    "show timesheet",
                    "show bug report",
                    "create bug report",
                    "edit bug report",
                    "delete bug report",
                    "move bug report",
                    "show gantt"
                ]),
            ],
            [
                'role' => 'Member',
                'permission' => json_encode([
                    "edit bug report",
                    "delete bug report",
                    "move bug report",
                    "show gantt"
                ]),
            ],
            [
                'role' => 'Teamlead',
                'permission' => json_encode([
                    "edit bug report",
                    "delete bug report",
           
                ]),
            ],
            // Add more role and permission sets as needed
        ];

        // Insert the permissions into the database
        WorkspacePermission::insert($permissions);
    }

   }

