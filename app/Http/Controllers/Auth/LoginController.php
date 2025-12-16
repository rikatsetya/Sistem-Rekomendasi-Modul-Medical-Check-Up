<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Custom login logic — local first, then API fallback.
     */
    public function login(Request $request)
    {
        $request->validate([
            'kopeg' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $token = env('API_TOKEN', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJkYXRhIjoiSGVsbG8sIFdvcmxkISIsImV4cGlyZWRfdG9rZW4iOiIyMDI2LTA2LTAzIDE0OjQ0OjA1In0.81M6qkPwrHN4qON2KKXZLjsGxMs0nNjW10TDQrYkzVs'); // Ganti dengan token yang sesuai
            $curl = curl_init();

            // Header Authorization dengan token
            $auth_data = array(
                'Bearer:' . $token,
            );

            // Data yang dikirimkan
            $data = array(
                'kopeg' => ($request->kopeg),
                'password' => ($request->password)
            );

            // Encode data menjadi JSON
            $body = json_encode($data);

            // Set cURL options
            curl_setopt($curl, CURLOPT_URL, 'https://hadir.jasatirta1.co.id/api/login');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $auth_data); // Header Authorization
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body); // Kirimkan data JSON sebagai body
            curl_setopt($curl, CURLOPT_TIMEOUT, 10); // timeout optional, untuk keamanan

            // Eksekusi cURL
            $result = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!$result) {
                throw new \Exception('Gagal menghubungi server API');
            }

            if ($httpCode !== 200) {
                throw new \Exception('Server API mengembalikan status ' . $httpCode);
            }

            $response = json_decode($result, true);

            if (isset($response['status']) && $response['status'] === true) {
                $personal = $response['data']['personal_data'];

                $user = User::where('kopeg', $personal['kopeg'])->where('name', $personal['full_name'])->first();
                if (!$user) {
                    $user = User::updateOrCreate(
                        [
                            'kopeg' => $personal['kopeg'],
                            'name' => $personal['full_name'],
                        ],
                        [
                            'gender' => $personal['gender'] ?? null,
                            'email' => $personal['email'] ?? null,
                            'password' => bcrypt(\Str::random(12)),
                            'peran' => 1,
                            'alamat' => $personal['address'] ?? null,
                            'divisi' => $personal['unit_name'] ?? null,
                        ]
                    );
                    $user->syncroles(1);
                }

                Auth::login($user);
                $request->session()->regenerate();

                return redirect()->intended($this->redirectTo);
            }

            // Jika login gagal (status false)
            return back()->withErrors([
                'kopeg' => 'Login gagal. Periksa kembali data Anda.',
            ])->withInput();
        } catch (\Exception $e) {
            Log::error('Login API error: ' . $e->getMessage());

            return back()->withErrors([
                'kopeg' => 'Terjadi kesalahan saat menghubungi sistem Hadir. Silakan coba lagi nanti.',
            ])->withInput();
        }
    }


    /**
     * Logout user safely
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Anda telah logout.');
    }
}
