<?php

use App\Models\Catatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes - Versi TANPA SANCTUM (Tidak Aman)
|--------------------------------------------------------------------------
*/

// --- ZONA CATATAN ---

Route::get('/catatan', function (Request $request) {
    // Tampilkan semua catatan, tidak peduli siapa pemiliknya
    $data = Catatan::with('user')->get();
    return response()->json($data);
});

Route::post('/catatan', function (Request $request) {
    // Validasi
    $request->validate([
        'judul' => 'required',
        'isi' => 'required',
        'user_id' => 'required|exists:users,id' // <-- user_id DIKIRIM MANUAL
    ]);

    $catatan = new Catatan();
    $catatan->judul = $request->input('judul');
    $catatan->isi = $request->input('isi');
    $catatan->user_id = $request->input('user_id'); // <-- Diisi manual
    $catatan->save();

    return response()->json($catatan, 201);
});

Route::get('/catatan/{id}', function ($id) {
    $catatan = Catatan::with('user')->find($id);

    if (!$catatan) {
        return response()->json(['message' => 'Catatan tidak ditemukan'], 404);
    }
    return response()->json($catatan);
});

Route::put('/catatan/{id}', function (Request $request, $id) {
    $catatan = Catatan::find($id);

    if (!$catatan) {
        return response()->json(['message' => 'Catatan tidak ditemukan'], 404);
    }
    
    $catatan->judul = $request->input('judul');
    $catatan->isi = $request->input('isi');
    
    // User_id bisa diubah-ubah secara bebas
    if ($request->has('user_id')) {
        $catatan->user_id = $request->input('user_id');
    }
    
    $catatan->save();
    return response()->json($catatan);
});

Route::delete('/catatan/{id}', function ($id) {
    $catatan = Catatan::find($id);

    if (!$catatan) {
        return response()->json(['message' => 'Catatan tidak ditemukan'], 404);
    }
    
    $catatan->delete();
    return response()->json(['message' => 'Catatan berhasil dihapus']);
});

// --- ZONA USER ---

Route::get('/users', function() {
    $users = User::all();
    return response()->json($users);
});

Route::post('/users', function(Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8'
    ]);

    $user = new User();
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response()->json($user, 201);
});

Route::get('/users/{id}', function($id) {
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }
    return response()->json($user);
});

// --- ZONA MAPPING ---

Route::get('/users/{id}/catatan', function($id) {
    $user = User::with('catatans')->find($id);
    if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }
    return response()->json($user);
});
// bikin api untuk user
// crud user, kemudian maping catatan ke user tertentu
// modifikasi crud catatan dengan menambahkan user sebagai pemilik catatan