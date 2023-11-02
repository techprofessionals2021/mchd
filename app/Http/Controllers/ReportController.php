<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\ModelHasRole;

class ReportController extends Controller
{
    public function index($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $model_has_role = ModelHasRole::get();

        // dd($model_has_role);


        return view('reports.index',compact('currentWorkspace'));
    }

}
