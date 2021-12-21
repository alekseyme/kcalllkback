<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Manager;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $managers = Manager::orderBy('name', 'ASC')->get();
        return response($managers, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $manager = Manager::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 200,
            'name' => $manager->name,
            'email' => $manager->email,
            'message' => 'Менеджер успешно создан',
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
        $manager = Manager::find($id);
        return $manager;
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
        $manager = Manager::find($id);

        $manager->name = $request->input('name');
        $manager->email = $request->input('email');
        $manager->phone = $request->input('phone');

        $manager->update();

        return response()->json([
            'message' => 'Менеджер успешно обновлён'
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
        $manager = Manager::find($id);
        $manager->delete();

        return response()->json([
            'message' => 'Менеджер успешно удалён'
        ]);
    }
}
