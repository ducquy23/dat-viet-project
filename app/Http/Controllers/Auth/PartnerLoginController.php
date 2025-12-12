<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PartnerLoginController extends Controller
{
    /**
     * Xử lý đăng nhập cho đối tác (từ modal)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Kiểm tra user có tồn tại và không phải admin
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        // Không cho admin đăng nhập vào site đối tác
        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản admin không thể đăng nhập vào site đối tác. Vui lòng đăng nhập tại /admin'],
            ]);
        }

        // Thử đăng nhập với guard partner
        if (Auth::guard('partner')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Nếu là AJAX request (từ modal), trả về JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'redirect' => route('listings.my-listings')
                ]);
            }
            
            return redirect()->intended(route('listings.my-listings'))
                ->with('success', 'Đăng nhập thành công');
        }

        // Nếu là AJAX request, trả về JSON error
        if ($request->expectsJson() || $request->ajax()) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Thông tin đăng nhập không chính xác.'],
        ]);
    }

    /**
     * Đăng xuất đối tác
     */
    public function logout(Request $request)
    {
        Auth::guard('partner')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đã đăng xuất thành công');
    }
}

