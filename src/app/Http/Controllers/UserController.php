<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\MailRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            $work_start = Attendance::where('user_id', $user->id)->whereDate('created_at', Carbon::today()->toDateString())->get();

            $work_end = Attendance::where('user_id', $user->id)->whereDate('updated_at', Carbon::today()->toDateString())->get();

            $break_start = collect();
            $break_end = collect();

            foreach ($work_start as $attendance) {
                $break_start = $break_start->merge(BreakTime::where('attendance_id', $attendance->id)->whereDate('created_at', Carbon::today()->toDateString())->get());
                $break_end = $break_end->merge(BreakTime::where('attendance_id', $attendance->id)->whereDate('updated_at', Carbon::today()->toDateString())->get());
            }

            return view('index', compact('work_start', 'work_end', 'break_start', 'break_end'));
        } else {
            return redirect('/login');
        }
    }

    public function viewLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ],[
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        return back()->with(
            'error_message', 'メールアドレスかパスワードが一致しません'
        );
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('message', 'ログアウトしました');
    }

    // ユーザー登録ページの表示
    public function viewRegister(Request $request)
    {
        $user_id = $request->query('id');
        return view('register', compact('user_id'));
    }

    // ユーザー登録処理
    public function register(UserRequest $request) {
        $user_id = $request->input('user_id');
        $user = User::find($user_id);

        // ユーザーが見つからない場合の処理
        if (!$user) {
            return redirect('/verify_email')->with('error_message', 'メールアドレスを認証してください');
        }

        $user->update([
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'email_verified' => '1',
        ]);

        Auth::login($user);

        return redirect('login')->with('message', '登録が完了しました');
    }

    // メール認証ページの表示
    public function viewVerifyEmail()
    {
        return view ('auth.emails.email_verify');
    }

    // メール認証処理
    public function verifyEmail(MailRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'email_verify_token' => base64_encode($request['email']),
        ]);
        // メール確認リンクの生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        // 認証メールの送信
        Mail::to($user->email)->send(new EmailVerification($user, $verificationUrl));

        return view ('auth.emails.verified', compact('verificationUrl'));
    }

    // 認証メール内リンククリック時の処理
    public function emailVerified (Request $request, $id, $hash)
    {
        $user = User::find($id);
        // ユーザーが見つからない場合
        if (!$user) {
            return redirect('/verify_email')->with('error_message', 'ユーザーが見つかりません');
        }
        // hashの確認
        if (sha1($user->email) !== $hash) {
            return redirect ('/verify_email')->with('error_message', 'リンクが正しくありません');
        }
        // メール確認のマーク
        if(!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return redirect()->route('viewRegister', ['id'=> $id]);
    }

    // 認証メールの再送信ページの表示
    public function viewResendForm()
    {
        return view('auth.emails.resend');
    }

    // 認証メールの再送信処理
    public function resend(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return redirect ('/resend')->with('error_message', '認証されていないアドレスです');
        }
        if($user->email_verified == 1){
            return redirect ('/resend')->with('error_message', '認証済のアドレスです。ログインページからログインしてください。');
        }
        $user->created_at = Carbon::now();
        // メール確認リンクの生成
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        Mail::to($user->email)->send(new EmailVerification($user, $verificationUrl));
        return view('auth.emails.verified');
    }
}
