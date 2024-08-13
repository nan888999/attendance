@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('title','会員登録')

@section('content')
<div class="content__item">
  <form class="form" action="/register" method="post">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user_id }}">
    <input class="form__input" type="text" name="name" placeholder="名前" value="{{ old('name') }}">
    <div class="form__error">
      @error('name')
      {{ $message }}
      @enderror
    </div>
    <input class="form__input" type="password" name="password" placeholder="パスワード" value="{{ old('password') }}">
    <div class="form__error">
      @error('password')
      {{ $message }}
      @enderror
    </div>
    <input class="form__input" type="password" name="password_confirmation" placeholder="確認用パスワード" value="{{ old('password_confirmation') }}">
    <div class="form__error">
      @error('password_confirmation')
      {{ $message }}
      @enderror
    </div>
    <button class="form__submit" type="submit">会員登録</button>
  </form>
  <div class="comment">
    アカウントをお持ちの方はこちらから<br>
    <a href="/login">ログイン</a>
  </div>
</div>
@endsection