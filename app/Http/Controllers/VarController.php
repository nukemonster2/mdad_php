<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class VarController extends Controller
{
    public function image($id)
    {
        $user = User::where([['id', '=', $id]])->first();
        $fileName = $user['img_url'];
        $path = public_path() . '/img/' . $fileName;
        return Response::download($path);
    }
}
