<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function list(){
        $data['getRecord'] = User::getAdmin();
        $data['header_title'] = "Admin List";
        return view('admin.admin.list', $data);
    }

    public function add(){
        $data['header_title'] = "Add New Admin";
        return view('admin.admin.add', $data);
    }

    public function insert(Request $request){

        $user = new User();
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        $user->user_type = 1;
        $user->save();

        return redirect('admin/admin/list')->with('success', 'Admin successfully created');
    }

    public function edit($id){
        $data['getRecord'] = User::getSingle($id);
        if(!empty($data['getRecord'])){
            $data['header_title'] = "Edit Admin";
            return view('admin.admin.edit', $data);
        } else{
            abort(404);
        }
       
    }

    public function update(Request $request, $id)
    {
        $user = User::getSingle($id);
        if (!$user) {
            return redirect('admin/admin/list')->with('error', 'User not found');
        }
    
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }
    
        $user->update();
    
        return redirect('admin/admin/list')->with('success', 'Admin successfully updated');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
        ]);
    
        $user = User::getSingle($request->id);
        $user->is_delete = 1;
        $user->save();
    
        return redirect('admin/admin/list')->with('success', 'Admin successfully deleted');
    }
    
}
