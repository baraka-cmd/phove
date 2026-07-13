<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $query = http_build_query([
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'access_type' => 'online',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $client = new \GuzzleHttp\Client([
                'verify' => false,
            ]);

            // Échanger le code contre un token
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $request->code,
                    'client_id' => env('GOOGLE_CLIENT_ID'),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                    'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
                    'grant_type' => 'authorization_code',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            // Récupérer les infos utilisateur
            $userResponse = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $googleUser = json_decode($userResponse->getBody(), true);

            $user = User::where('email', $googleUser['email'])->first();

            if ($user) {
                if ($user->statut !== 'actif') {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Votre compte est ' . $user->statut . '.',
                    ]);
                }
                if (!$user->photo_profil) {
                    $user->update(['photo_profil' => $googleUser['picture'] ?? null]);
                }
            } else {
                $user = User::create([
                    'nom' => $googleUser['name'] ?? 'Utilisateur Google',
                    'email' => $googleUser['email'],
                    'mot_de_passe' => bcrypt(Str::random(32)),
                    'role' => 'editeur',
                    'statut' => 'actif',
                    'photo_profil' => $googleUser['picture'] ?? null,
                ]);
            }

            Auth::login($user);
            $user->update(['derniere_connexion' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Erreur Google : ' . $e->getMessage(),
            ]);
        }
    }
}