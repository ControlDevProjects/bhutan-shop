<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller {
    public function index() {
        $employees = User::whereIn('role',['admin','employee'])->withCount('assignedOrders')->latest()->get();
        return view('admin.employees.index', compact('employees'));
    }
    public function create() { return view('admin.employees.create'); }
    public function store(Request $request) {
        $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','role'=>'required|in:admin,employee','password'=>'required|min:6|confirmed']);
        User::create(['name'=>$request->name,'email'=>$request->email,'phone'=>$request->phone,'role'=>$request->role,'is_active'=>$request->boolean('is_active',true),'password'=>bcrypt($request->password)]);
        return redirect()->route('admin.employees.index')->with('success','Employee account created.');
    }
    public function edit(User $employee) { return view('admin.employees.edit', compact('employee')); }
    public function update(Request $request, User $employee) {
        $request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email,'.$employee->id,'role'=>'required|in:admin,employee','password'=>'nullable|min:6|confirmed']);
        $data = $request->only(['name','email','phone','role']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->filled('password')) $data['password'] = bcrypt($request->password);
        $employee->update($data);
        return redirect()->route('admin.employees.index')->with('success','Employee updated.');
    }
    public function destroy(User $employee) {
        if ($employee->id === auth()->id()) return back()->with('error','You cannot delete yourself.');
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success','Employee deleted.');
    }
}
