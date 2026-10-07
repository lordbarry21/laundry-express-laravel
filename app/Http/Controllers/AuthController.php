<?php

// Menentukan namespace lokasi AuthController

namespace App\Http\Controllers;

// Mengimpor class Request untuk menangani kiriman data HTTP
use Illuminate\Http\Request;
// Mengimpor Facade Auth untuk mengelola proses autentikasi sesi login
use Illuminate\Support\Facades\Auth;

// Mendefinisikan class AuthController untuk fitur login dan logout admin
class AuthController extends Controller
{
    /**
     * Menampilkan form halaman login admin
     */
    public function loginForm()
    {
        // Memeriksa jika admin saat ini sudah dalam keadaan login
        if (Auth::check()) {
            // Jika sudah login, langsung alihkan ke halaman dashboard admin
            return redirect()->route('admin.dashboard');
        }

        // Jika belum login, tampilkan halaman view auth.login
        return view('auth.login');
    }

    /**
     * Memproses autentikasi kredensial login admin dari formulir
     */
    public function login(Request $request)
    {
        // Melakukan validasi input form bahwa username dan password wajib diisi
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username tidak boleh kosong!',
            'password.required' => 'Password tidak boleh kosong!',
        ]);

        // Mengambil hanya kredensial username dan password dari request
        $credentials = $request->only('username', 'password');

        // Mencoba mencocokkan data login menggunakan fitur Auth::attempt() bawaan Laravel
        if (Auth::attempt($credentials)) {
            // Meregenerasi session ID untuk mencegah serangan pembajakan sesi (Session Fixation)
            $request->session()->regenerate();

            // Mengalihkan ke dashboard admin setelah berhasil login
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali di panel administrator!');
        }

        // Jika username atau password tidak cocok, kembalikan ke form login dengan pesan kesalahan
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->withInput($request->only('username'));
    }

    /**
     * Memproses logout admin dan menghapus sesi
     */
    public function logout(Request $request)
    {
        // Mengeluarkan status login user dari sistem Laravel Auth
        Auth::logout();

        // Mengosongkan data sesi yang sedang aktif
        $request->session()->invalidate();

        // Membuat ulang token CSRF baru demi keamanan
        $request->session()->regenerateToken();

        // Mengalihkan kembali ke halaman login dengan pesan notifikasi
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
