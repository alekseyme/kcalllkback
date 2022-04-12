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
        $projects = Project::with('users')->orderBy('name', 'ASC')->get();
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
    
    public function editrow(Request $request, $id)
    {
        $base_prefix = '_base_';

        $row = DB::table($base_prefix.$request->project)->where('id', $id);

        $updateData = $request->except(['project']);

        $row->update($updateData);

        return response()->json([
            'message'=>'Запись успешно обновлена',
            'row'=>$row->first()
        ]);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project = Project::create([
            'name' => $request->name,
            'tablename' => $request->tablename,
            'base_header' => $request->base_header,
            'base_row' => $request->base_row,
        ]);

        if($request->input('users'))
        {
            $project->users()->attach($request->input('users'));
        }

        return response()->json([
            'status' => 200,
            'name' => $project->name,
            'tablename' => $project->tablename,
            'message' => 'Проект успешно создан',
        ]);
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

        $project->name = $request->input('name');
        $project->tablename = $request->input('tablename');
        $project->base_header = $request->input('base_header');
        $project->base_row = $request->input('base_row');

        $project->update();

        $project->users()->detach();
        if($request->input('users'))
        {
            $project->users()->attach($request->input('users'));
        }

        return response()->json([
            'message' => 'Проект успешно обновлён'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $project = Project::find($id);
        $project->users()->detach();
        $project->delete();

        return response()->json([
            'message' => 'Проект успешно удалён'
        ]);
    }
}
