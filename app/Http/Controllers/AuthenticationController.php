<?php

namespace App\Http\Controllers;

use App\SimpsonsQuotes\Authenticator;
use Illuminate\Http\Request;

class AuthenticationController
{
    protected Authenticator $authenticator;
    protected Request $request;

    public function __construct(Authenticator $authenticator, Request $request)
    {
        $this->authenticator = $authenticator;
        $this->request = $request;
    }

    public function getLoginToken(): array
    {
        $this->request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $this->request->get('email');
        $password = $this->request->get('password');

        return $this->authenticator->getLoginToken($email, $password);
    }
}
