<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\User;
use DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderBy('name', 'ASC')->get();
        return response($projects, 200);
    }

    public function userprojects()
    {   
        if (auth()->user()->isadmin) {
            $projects = Project::orderBy('name', 'ASC')->get();
            return response($projects, 200);
        }

        $projects = auth()->user()->projects()->orderBy('name', 'ASC')->get();

        return response($projects, 200);
    }

    public function search(Request $request)
    {   
        $activeproject = $request->project;
        $per_page = $request->per_page;

        $base_prefix = '_base_';

        $query = DB::connection('mysql2')->table($base_prefix.$activeproject);

        $uniquestatus = DB::connection('mysql2')->table($base_prefix.$activeproject)->select('status')->distinct()->pluck('status');

        if ($request->filled('from')) {
            $query->where('time', '>=', $request->from.' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('time', '<=', $request->to.' 23:59:59');
        }

        if ($request->filled('phone')) {
            $query->where('number', 'LIKE', '%'.$request->phone.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $project = $query->orderBy('id', 'DESC')->paginate($per_page ? $per_page : 10);

        return response()->json([
            'paginate' => $project,
            'statuses' => $uniquestatus
        ]);
    }

    public function export(Request $request)
    {   
        $base_prefix = '_base_';
        $activeproject = $request->project;

        $query = DB::connection('mysql2')->table($base_prefix.$activeproject);

        if ($request->filled('from')) {
            $query->where('time', '>=', $request->from.' 00:00:00');
        }

        if ($request->filled('to')) {
            $query->where('time', '<=', $request->to.' 23:59:59');
        }

        if ($request->filled('phone')) {
            $query->where('number', 'LIKE', '%'.$request->phone.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $project = $query->select($request->fields)->orderBy('id', 'DESC')->get();

        return response($project, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {        
        $project = Project::with('users')->find($id);
        return $project;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        // $project->name = $request->input('name');
        // $project->tablename = $request->input('tablename');
        // $project->table_header_client = $request->input('table_header_client');
        // $project->table_row_client = $request->input('table_row_client');

        // $project->update();

        $project->users()->detach();
        if($request->input('users'))
        {
            $project->users()->attach($request->input('users'));
        }

        return response()->json([
            'message' => 'Проект успешно обновлён'
        ]);
    }
}
