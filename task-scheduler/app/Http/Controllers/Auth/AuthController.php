<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => 'default-avatar.png'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->remember) {
            Auth::setRememberDuration(1440); // 设置为1天（24小时 * 60分钟）
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->remember)) {
            return response()->json([
                'message' => '登录凭证无效'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => '已成功登出']);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => '该邮箱未注册'], 404);
        }

        $user->password = Hash::make('123');
        $user->save();

        return response()->json([
            'message' => '密码已重置为123，请尽快登录并修改密码以确保账户安全'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => '密码重置成功']);
        }

        return response()->json(['message' => '无法重置密码'], 400);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateUser(Request $request)
    {
        // 打印调试信息
        info('开始处理更新用户请求');
        info('请求数据:', $request->all());
        info('当前认证用户ID: ' . ($request->user() ? $request->user()->id : 'null'));
        info('请求路径: ' . $request->path());
        info('请求方法: ' . $request->method());
        
        $user = $request->user();
        info('获取到用户信息:', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:3|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 如果要更新密码，验证当前密码
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'errors' => ['current_password' => ['旧密码不正确']]
                ], 422);
            }
            
            // 检查新密码是否与当前密码相同
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'errors' => ['password' => ['新密码不能与旧密码相同']]
                ], 422);
            }
            
            $user->password = Hash::make($request->password);
        }

        // 更新用户名
        $user->name = $request->name;
        $user->save();

        return response()->json([
            'message' => '用户信息更新成功',
            'user' => $user
        ]);
    }
}