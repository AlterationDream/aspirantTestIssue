<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login() {
        return view('auth.login');
    }

    public function authenticate(Request $request) {
        $credentials = $this->validate($request, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:32',
        ],[
            'email.required' => 'Email - необходимое поле.',
            'email.email' => 'Введён некорректный email.',
            'email.max' => 'Поле email не может содержать больше 255 символов.',
            'password.required' => 'Пароль - необходимое поле',
            'password.min' => 'Пароль не может быть короче 8 символов',
            'password.max' => 'Пароль не может быть длиннее 32 символов',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->to('/');
        }

        return back()->withErrors(['msg' => 'Пользователь с таким email или паролем не найден.']);
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function register() {
        return view("auth.register");
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|regex:/^[A-Za-zа-яёА-ЯЁ0-9_.,() ]+$/u|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ],[
            'name.regex' => 'Имя может состоять только из букв, цифр, пробелов и специальных знаков.',
            'name.max' => 'Поле имя не может содержать больше 255 символов.',
            'name.required' => 'Имя - необходимое поле.',
            'email.required' => 'Email - необходимое поле.',
            'email.email' => 'Введён некорректный email.',
            'email.max' => 'Поле email не может содержать больше 255 символов.',
            'email.unique' => 'Данный email уже зарегистрирован.',
            'password.required' => 'Пароль - необходимое поле',
            'password.min' => 'Пароль не может быть короче 8 символов',
            'password.max' => 'Пароль не может быть длиннее 32 символов',
            'password_confirmation.same' => "Поля пароль и подтверждение пароля не совпадают.",
            'password_confirmation.required' => "Подтверждение пароля - необходимое поле.",
            'password_confirmation.min' => "Подтверждение пароля не может быть короче 8 символов.",
            'password_confirmation.max' => "Подтверждение пароля не может быть длиннее 32 символов."
            ]);

        $user = $this->user->create([
            'name' => $request->name,
            'email' => $request->email,
            'password'=> bcrypt($request->password),
        ]);

        Auth::login($user, true);
        return redirect()->to('/');
    }
}
