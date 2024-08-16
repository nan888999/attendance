@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('title','ログイン')

@section('content')
<div class="content__item">
  <form class="form" action="/login" method="post">
    @csrf
    <input class="form__input" type="text" name="email" placeholder="メールアドレス" value="{{ old('email') }}">
      <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
      </div>
    <input class="form__input" type="password" name="password" placeholder="パスワード">
      <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
      </div>
    <button class="form__submit" type="submit">ログイン</button>
  </form>
  <div class="comment">
    アカウントをお持ちでない方はこちらから<br>
    <a href="/verify_email">会員登録</a>
  </div>
</div>
@endsection