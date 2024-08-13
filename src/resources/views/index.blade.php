@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('header')
  <nav class="header-nav">
    <ul class="header-nav-list">
      <li class="header-nav-item"><a href="/">ホーム</a></li>
      <li class="header-nav-item"><a href="/attendance">日付一覧</a></li>
      <li class="header-nav-item">
        <form class="logout" action="{{route('user.logout')}}" method="post">
          @csrf
          <input class="logout" type="submit" value="ログアウト">
        </form>
      </li>
    </ul>
  </nav>
@endsection

@section('content')
<div class="content__title">{{\Illuminate\Support\Facades\Auth::user()->name}}さんお疲れ様です！</div>
<div class="content__item">
  <form action="/workStart" method="post">
    @csrf
    @if($work_start->isEmpty())
      <button type="submit" class="available">勤務開始</button>
      @else
      <button type="button" class="non-available">勤務開始</button>
    @endif
  </form>
  <form action="/work_end" method="post">
    @csrf
    @if(!$work_start->isEmpty() && $work_start->last()->created_at == $work_end->last()->updated_at)
      <button type="submit" class="available">勤務終了</button>
      @else
      <button type="button" class="non-available">勤務終了</button>
    @endif
  </form>
</div>
<div class="content__item">
  <form action="/breakStart" method="post">
    @csrf
    @if(!$work_start->isEmpty() && ($work_start->last()->created_at == $work_end->last()->updated_at) && ($break_start->isEmpty() || $break_start->last()->created_at != $break_end->last()->updated_at))
      <button type="submit" class="available">休憩開始</button>
      @else
      <button type="button" class="non-available">休憩開始</button>
    @endif
  </form>
  <form action="/breakEnd" method="post">
    @csrf
    @if(!$work_start->isEmpty() && ($work_start->last()->created_at == $work_end->last()->updated_at) && (!$break_start->isEmpty() && $break_start->last()->created_at == $break_end->last()->updated_at))
      <button type="submit" class="available">休憩終了</button>
      @else
      <button type="button" class="non-available">休憩終了</button>
    @endif
  </form>
</div>
@endsection