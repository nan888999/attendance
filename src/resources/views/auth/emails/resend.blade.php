@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('title','メールアドレス登録')

@section('content')
<div class="content__item">
  <p>メールアドレスの再認証を行います。<br>
  メールアドレスを入力し、「メール送信」ボタンを押してください。</p>
  <form class="form" action="/resend" method="post">
    @csrf
    <input class="form__input" type="text" name="email" placeholder="メールアドレス" value="{{ old('email') }}">
    <div class="form__error">
      @error('email')
      {{ $message }}
      @enderror
    </div>
    <button class="form__submit" type="submit">メール送信</button>
  </form>
  <div class="comment">
    アカウントをお持ちの方はこちらから<br>
    <a href="/login">ログイン</a>
  </div>
</div>
@endsection