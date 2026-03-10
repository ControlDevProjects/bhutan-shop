<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash};

class ProfileController extends Controller {
    public function edit() {
        return view('frontend.auth.profile');
    }

    public function update(Request $request) {
        $user = Auth::user();
        if ($request->update_type === 'password') {
            $request->validate(['current_password'=>'required','password'=>'required|min:6|confirmed']);
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password'=>'Current password is incorrect.']);
            }
            $user->update(['password'=>bcrypt($request->password)]);
            return back()->with('success','Password updated.');
        }
        $request->validate(['name'=>'required|string|max:100','phone'=>'nullable|string|max:20']);
        $user->update($request->only(['name','phone','address','city','dzongkhag']));
        return back()->with('success','Profile updated.');
    }
}
