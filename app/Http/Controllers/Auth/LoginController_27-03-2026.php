<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    public function showLogin() {
        if (Auth::check()) return $this->redirectAfterLogin();
        return view('frontend.auth.login');
    }

    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password,'is_active'=>true])) {
            $request->session()->regenerate();
            return $this->redirectAfterLogin();
        }
        return back()->withErrors(['email'=>'Invalid credentials or account inactive.'])->withInput();
    }

    public function logout(Request $request) {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectAfterLogin() {
        if (Auth::user()->isStaff()) return redirect()->route('admin.dashboard');
        return redirect()->intended(route('home'));
    }

    public function showRegister() {
        if (Auth::check()) return redirect()->route('home');
        return view('frontend.auth.register');
    }

    public function register(Request $request) {
        $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','phone'=>'nullable|string|max:20','password'=>'required|min:6|confirmed']);
        Auth::login(\App\Models\User::create(['name'=>$request->name,'email'=>$request->email,'phone'=>$request->phone,'password'=>bcrypt($request->password),'role'=>'customer']));
        return redirect()->route('home')->with('success','Welcome to Bhutan Shop! 🐉');
    }
}
