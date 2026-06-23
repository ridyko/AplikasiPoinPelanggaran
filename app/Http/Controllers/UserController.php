<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function checkSuperAdmin()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya Super Admin yang memiliki akses ke halaman ini.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $users   = User::with('classes')->orderBy('name')->get();
        $classes = Kelas::with('homeroomTeacher')->orderBy('class_name')->get();

        return view('users.index', compact('users', 'classes'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:super_admin,guru_bk,wakil_kesiswaan,wali_kelas,guru',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        if ($validated['role'] === 'wali_kelas' && empty($request->input('class_id'))) {
            return back()->withErrors(['class_id' => 'Wali Kelas wajib dipetakan ke satu kelas.'])->withInput();
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        // Map class to the new Wali Kelas
        if ($validated['role'] === 'wali_kelas' && !empty($validated['class_id'])) {
            $kelas = Kelas::find($validated['class_id']);
            if ($kelas) {
                // Remove old mapping if another teacher is assigned
                $kelas->homeroom_teacher_id = $user->id;
                $kelas->save();
            }
        }

        return redirect()->route('users')->with('success', "User '{$user->name}' berhasil didaftarkan!");
    }

    public function edit(User $user)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $classes = Kelas::with('homeroomTeacher')->orderBy('class_name')->get();
        return view('users.edit', compact('user', 'classes'));
    }

    public function update(Request $request, User $user)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role'     => 'required|in:super_admin,guru_bk,wakil_kesiswaan,wali_kelas,guru',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        if ($validated['role'] === 'wali_kelas' && empty($request->input('class_id'))) {
            return back()->withErrors(['class_id' => 'Wali Kelas wajib dipetakan ke satu kelas.'])->withInput();
        }

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        // Clear any old class mappings for this teacher first
        Kelas::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);

        // If wali_kelas, assign new class
        if ($validated['role'] === 'wali_kelas' && !empty($validated['class_id'])) {
            $kelas = Kelas::find($validated['class_id']);
            if ($kelas) {
                $kelas->homeroom_teacher_id = $user->id;
                $kelas->save();
            }
        }

        return redirect()->route('users')->with('success', "Data user '{$user->name}' berhasil diperbarui!");
    }

    public function destroy(User $user)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users')->with('error', 'Tidak dapat menghapus akun Anda sendiri!');
        }

        $name = $user->name;
        // Unset homeroom teacher
        Kelas::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);
        $user->delete();

        return redirect()->route('users')->with('success', "User '{$name}' berhasil dihapus.");
    }

    /**
     * Remap a Wali Kelas to a different class (without changing other data)
     */
    public function remap(Request $request, User $user)
    {
        if ($redirect = $this->checkSuperAdmin()) return $redirect;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        if ($user->role !== 'wali_kelas') {
            return redirect()->route('users')->with('error', 'Hanya Wali Kelas yang bisa dipetakan ulang ke kelas.');
        }

        // Clear old mapping
        Kelas::where('homeroom_teacher_id', $user->id)->update(['homeroom_teacher_id' => null]);

        // Assign new class
        $kelas = Kelas::find($validated['class_id']);
        if ($kelas) {
            $kelas->homeroom_teacher_id = $user->id;
            $kelas->save();
        }

        return redirect()->route('users')->with('success', "Wali Kelas '{$user->name}' berhasil dipetakan ke kelas '{$kelas->class_name}'!");
    }
}
