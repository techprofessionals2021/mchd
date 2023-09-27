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

                    "invite user",
                    "create project",
                    "show calendar",
                    "show timesheet",
                    "project report"
                
                ]),
            ],
            [
                'role' => 'Member',
                'permission' => json_encode([
                    "invite user",
                    "create project",
                    "show calendar"
            
                ]),
            ],
            [
                'role' => 'Teamlead',
                'permission' => json_encode([
                    "show calendar",
                    "show timesheet",
           
                ]),
            ],

            [
                'role' => 'Follower',
                'permission' => json_encode([
                    "show calendar",
                    "show timesheet",
           
           
                ]),
            ],

            [
                'role' => 'Hod',
                'permission' => json_encode([
                    "invite user",
                    "create project",
                    "show calendar",
                    "show timesheet",
                    "project report"
           
                ]),
            ],
            // Add more role and permission sets as needed
        ];

        // Insert the permissions into the database
        WorkspacePermission::insert($permissions);
    }

   }

