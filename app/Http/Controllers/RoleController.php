<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Managers;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function getRole(Request $request)
    {
        $request->validate([
            'RoleID' => 'required'
        ]);
        $name = Role::where([['RoleID', '=', $request['RoleID']]])->first();
        return response()->json([
            'Name' => $name['RoleName'],
            'message' => "SUCCEED",
            'success' => true,
        ]);
    }
    public function getAllemployees(Request $request)
    {
        $request->validate(([
            'name' => 'required'
        ]));
        $employees = Managers::where([['manager', '=', $request['name']]])->get();
        $user = User::select('*')->leftJoin('managers', 'managers.name', '=', 'users.name')
            ->get();
        if ($employees) {
            return response()->json([
                'list' => $employees,
                'success' => true,
            ]);
        } else {
            return response()->json([
                'list' => '',
                'success' => false,
            ]);
        }
    }
}
