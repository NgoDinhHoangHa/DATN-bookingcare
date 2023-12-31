<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\user\Rq_resgister;
use App\Http\Requests\user\Rq_login;
use App\Models\User\M_users;
use App\Models\User\M_confirm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

// session_start();

class C_User extends Controller
{
    public function confirmEmail(Rq_resgister $request)
    {
        $Check = rand(100000, 999999);
        $data = [
            'number' => $Check
        ];
        \Mail::to($request->email)->send(new \App\Mail\SendMail($data, 'Confirm'));
        M_confirm::whereNotNull('id')->delete();

        $confirm = new M_confirm;
        $confirm->email = $request->email;
        $confirm->code = $Check;
        $confirm->save();
        return true;
    }
    public function register(Rq_resgister $request)
    {
        $check = M_confirm::where('email', $request->email)->get();
        if ($request->code == $check[0]->code) {
            $user = new M_users;
            $user->fullname = $request->fullname;
            $user->email = $request->email;
            $user->gender = $request->gender;
            $user->avatar = "avatar-default.jpeg";
            $user->status = 1;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            $user->save();
            return;
        } else {
            return response()->json(['message' => 'Mã xác nhận không chính xác!'], 401);
        }
    }
    public function login(Rq_login $request)
    {
        if (auth('user')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        ])) {
            $user = M_users::where('email', '=', $request->email)->first();
            $user->token = $user->createToken('User', ['user'])->accessToken;
            return response()->json($user);
        } else {
            return response()->json(['message' => 'Sai tên đăng nhập hoặc mật khẩu!'], 401);
        }
    }

    public function userAuth(Request $request)
    {
        return response()->json($request->user('api_user'));
    }

    public function getAllUser()
    {
        $result = DB::select("SELECT * FROM m_users ORDER BY m_users.created_at DESC");
        return (response()->json(['data' => $result]));
    }

    public function getUserLimit()
    {
        $result = DB::select("SELECT * FROM m_users ORDER BY m_users.created_at DESC LIMIT 0,10");
        return (response()->json(['data' => $result]));
    }

    public function searchUser(Request $request)
    {
        $result = DB::select("SELECT * FROM m_users WHERE (m_users.fullname LIKE '%" . $request->value . "%' OR 
        m_users.phone LIKE '%" . $request->value . "%' OR m_users.email LIKE '%" . $request->value . "%') " .
            ($request->status !== null ? ' AND m_users.status = ' . $request->status : '') . " ORDER BY m_users.created_at DESC");
        return response()->json(['data' => $result]);
    }

    public function updateAvatar(Request $request)
    {
        DB::table('m_users')->where('id', $request->id)->update([
            'avatar' => $request->image
        ]);
        if ($request->current !== "avatar-default.jpeg") {
            if (File::exists(public_path('images/' . $request->current))) {
                File::delete(public_path('images/' .  $request->current));
            }
        }
        $result = DB::select('SELECT * FROM m_users WHERE id = ? ', [$request->id]);
        return response()->json(['data' => sizeof($result) === 0 ? null : $result[0]]);
    }

    public function updateStatus(Request $request)
    {
        DB::table('m_users')->where('m_users.id', $request->id)->update([
            'status' => $request->status
        ]);
        $result = DB::select("SELECT * FROM m_users WHERE m_users.id = $request->id");
        return response()->json(['data' => sizeof($result) === 0 ? null : $result[0]]);
    }

    public function updateUser(Request $request)
    {
        $user = DB::select('SELECT * FROM m_users WHERE id = ? ', [$request->id]);
        $user = sizeof($user) === 0 ? null : $user[0];
        if ($user) {
            $password = $request->updatePassword === 'true' ? Hash::make($request->password) : $user->password;
            DB::table('m_users')->where('id', $request->id)->update([
                'password' => $password,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender
            ]);
            $result = DB::select('SELECT * FROM m_users WHERE id = ? ', [$request->id]);
            return response()->json(['data' => sizeof($result) === 0 ? null : $result[0]]);
        }
    }
}
