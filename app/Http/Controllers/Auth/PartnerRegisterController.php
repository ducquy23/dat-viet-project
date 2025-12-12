<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PartnerRegisterController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'agree_terms' => 'required|accepted',
        ], [
            'username.required' => 'Vui lòng nhập username',
            'username.unique' => 'Username đã tồn tại',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'agree_terms.required' => 'Bạn phải đồng ý với điều khoản sử dụng',
            'agree_terms.accepted' => 'Bạn phải đồng ý với điều khoản sử dụng',
        ]);

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Đối tác
            'status' => 'active',
        ]);

        Auth::guard('partner')->login($user);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'redirect' => route('listings.my-listings')
            ]);
        }

        return redirect()->route('listings.my-listings')
            ->with('success', 'Đăng ký thành công!');
    }
}

