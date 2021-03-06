<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;

class AuthController extends Controller
{
    /**
     * Display a view login
     *
     * @return \Illuminate\Http\Response
     */
    public function viewLogin()
    {
        return view('login');
    }

    /**
     * Handle login of user
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(
            $request->only(['email', 'password']),
            !!$request->remember
        )) {
            return redirect('/');
        }

        return redirect()
            ->back()
            ->with('errorLogin', 'Thông tin đăng nhập không chính xác.');
    }

    /**
     * Handle logout for user
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Display change profile screen
     *
     * @return \Illuminate\Http\Response
     */
    public function viewUpdateProfile()
    {
        return view('auth.update-profile');
    }

    /**
     * Handle update profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
            ];
            if ($request->hasFile('avatar')) {
                $data['avatar_path'] = $request->file('avatar')->store('public/avatars');
            }
            auth()->user()->update($data);
            DB::commit();
        } catch (\Throwable $th) {
            Storage::delete($data['avatar_path'] ?? null);
            DB::rollback();
            throw $th;
        }

        return redirect()->back()->with('successUpdateProfile', 'Cập nhật hồ sơ thành công.');
    }

    /**
     * Display change password screen for auth
     *
     * @return \Illuminate\Http\Response
     */
    public function viewChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Handle change password
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return redirect()->back()->with('errorChangePassword', 'Mật khẩu cũ không hợp lệ.');
        }

        try {
            DB::beginTransaction();
            auth()->user()->update([
                'password' => Hash::make($request->password)
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        return redirect()->back()
            ->with('successChangePassword', 'Đổi mật khẩu thành công.');
    }
}
