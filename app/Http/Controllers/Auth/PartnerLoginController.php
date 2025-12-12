<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PartnerLoginController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản admin không thể đăng nhập vào site đối tác. Vui lòng đăng nhập tại /admin'],
            ]);
        }

        if (Auth::guard('partner')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $redirectTo = $request->get('redirect_to', 'listings.my-listings');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'redirect' => route($redirectTo),
                    'openModal' => $request->get('open_modal')
                ]);
            }

            return redirect()->intended(route($redirectTo))
                ->with('success', 'Đăng nhập thành công');
        }
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
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function logout(Request $request): Redirector|RedirectResponse
    {
        Auth::guard('partner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Đã đăng xuất thành công');
    }
}

