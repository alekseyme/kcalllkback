<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Manager;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $users = User::with('manager')->orderBy('id', 'DESC')->get();
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'isadmin' => $request->isadmin,
            'manager_id' => $request->manager,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'status' => 200,
            'name' => $user->name,
            'message'=>'Пользователь успешно создан'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('manager')->find($id);
        $managers = Manager::get();

        return response()->json([
            'status' => 200,
            'user' => $user,
            'managers' => $managers
        ]);
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
        $user = User::find($id);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->isadmin = $request->input('isadmin');
        $user->manager_id = $request->input('manager');

        $user->update();

        return response()->json([
            'message'=>'Пользователь успешно обновлён'
        ]);
    }

    public function changepassword(Request $request)
    {
        $user = User::find($request->userid);

        $user->password = Hash::make($request->password);

        $user->update();

        return response()->json([
            'message' => 'Пароль успешно изменён. При следующем входе в систему, используйте новый пароль',
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
        $user = User::find($id);
        $user->projects()->detach();
        $user->delete();

        return response()->json([
            'message'=>'Пользователь успешно удалён'
        ]);
    }
}
