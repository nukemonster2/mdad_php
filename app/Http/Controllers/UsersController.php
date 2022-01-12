<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\Json;
use Carbon\Carbon;
use App\Models\User;
use App\Models\InvCode;
use App\Models\Managers;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Validator;

class UsersController extends Controller
{
  /**
   * Register api.
   *
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'status' => false,
        'message' => $validator->errors(),
      ], 401);
    }
    $input = $request->all();
    $user = User::create($input);
    $success['token'] = $user->createToken('appToken')->accessToken;
    return response()->json([
      'status' => true,
      $success,
      'user' => $user
    ]);
  }

  public function sendOtp(Request $request)
  {
    $otp = rand(100000, 999999);
    Log::info("otp = " . $otp);
    $user = User::where('email', '=', $request->email)->update(['otp' => $otp]);
    // send otp to email using email api
    return response()->json([
      'user' => $user,
      'otp' => $otp,
      'message' => 'Otp sent!'
    ], 200);
  }
  public function verifyOtp(Request $request)
  {
    Log::info($request);
    $user  = User::where([['otp', '=', request('otp')]])->first();
    if ($user) {
      User::where('otp', '=', $request->otp)->update(['otp' => null]);
      /*/return view('home');/*/
      return 'SUCCEED';
    } else {
      return 'FAILED';
    }
  }

  public function changeUserDetail(Request $request)
  {
    $request->validate([
      'id' => 'required|int',
      'email' => 'string|email',
      'name' => 'string',
      'phone' => 'int',
    ]);
    $updated = User::where('id', $request['id'])->update($request->all());
    $user = User::where([['id', '=', $request['id']]])->first();
    return response()->json([
      'status' => true,
      'user' => $user,
      'id' => $user['id'],
      'Updated name' => $user['name'],
      'Updated email' => $user['email'],
      'Updated number' => $user['phone'],
      'Updated?' => $updated
    ]);
  }

  /**
   * Login user and create token
   *
   * @param  [string] email
   * @param  [string] password
   * @param  [boolean] remember_me
   * @return [string] access_token
   * @return [string] token_type
   * @return [string] expires_at
   */
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);
    $credentials = request(['email', 'password']);
    if (Auth::attempt($credentials)) {
      $user = Auth::user();
      $success['token'] = $user->createToken('appToken')->accessToken;
      //After successfull authentication, notice how I return json parameters
      return response()->json([
        'status' => true,
        'token' => $success['token'],
        'user' => $user
      ]);
    } else {
      //if authentication is unsuccessfull, notice how I return json parameters
      return response()->json([
        'status' => false,
        'message' => 'Invalid Email or Password',
      ], 401);
    }
  }


  /**
   * Logout user (Revoke the token)
   *
   * @return [string] message
   */
  public function logout(Request $res)
  {
    if (Auth::user()) {
      $user = Auth::user()->token();
      $user->revoke();

      return response()->json([
        'success' => true,
        'message' => 'Logout successfully'
      ]);
    } else {
      return response()->json([
        'success' => false,
        'message' => 'Unable to Logout'
      ]);
    }
  }

  public function searchEmployee(Request $request)
  {
    $request->validate([
      'name' => 'string',
    ]);
    $result = User::query()
      ->where('name', 'LIKE', "%{$request['name']}%")
      ->get();
    return response()->json([
      'results' => $result
    ]);
  }

  public function getEmployeeDetails(Request $request)
  {
    $request->validate([
      'name' => 'string',
    ]);
    $result = User::where([['name', '=', $request['name']]])->first();
    $manager = Managers::where([['name', '=', $request['name']]])->first();
    return response()->json([
      'success' => true,
      'results' => $result,
      'manager' => $manager
    ]);
  }
  public function deleteUser(Request $request)
  {
    $request->validate([
      'phone' => 'int',
      'name' => 'string'
    ]);
    $result = User::where('phone', '=', $request['phone'])->delete();
    $role = Managers::where('name', '=', $request['name'])->delete();
    return response()->json([
      'success' => true,
      'results' => $result,
      'role' => $role
    ]);
  }
  /**
   * Get the authenticated User
   *
   * @return [json] user object
   */
  public function user(Request $request)
  {
    $user = $request->user();
    return response()->json([
      $user,
    ]);
  }
  public function insertImg(Request $request)
  {
    $request->validate([
      'id' => 'required|int',
    ]);

    $user = User::where([['id', '=', $request['id']]])->first();
    $fileName = $user['img_url'];
    $path = public_path() . '/img/' . $fileName;
    $blob = file_get_contents($path); //from database
    $url = 'data:image/jpeg;base64,' . base64_encode($blob) . '';
    return response()->json([
      'url' => $url,
      'status' => true
    ]);
  }
}
