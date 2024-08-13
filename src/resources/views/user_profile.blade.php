@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
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

@section('title', $user->name .'の勤務状況')

@section('content')
<div class="attendance__header">
<h1></h1>
</div>
<div class="attendance__table">
  <table class="attendance__table-body">
    <thead>
      <tr class="attendance__table-row">
        <th class="attendance__table-header">勤務日</th>
        <th class="attendance__table-header">勤務開始</th>
        <th class="attendance__table-header">勤務終了</th>
        <th class="attendance__table-header">休憩時間</th>
        <th class="attendance__table-header">勤務時間</th>
      </tr>
    </thead>
    <tbody>
      @foreach($attendances as $attendance)
      <tr class="attendance__table-row">
          <td class="attendance__table-data">
            {{ $attendance->created_at->format('Y/m/d') ?? '' }}
          </td>
          <td class="attendance__table-data">{{ $attendance->created_at->format('H:i:s') ?? '' }}</td>
          <td class="attendance__table-data">
            @if($attendance->created_at == $attendance->updated_at)
            {{ '' }}
            @else
            {{ $attendance->updated_at->format('H:i:s') }}
            @endif
          </td>
        <td class="attendance__table-data">
          {{ $formattedBreakTimes[$attendance->id] ?? '00:00:00' }}
        </td>
        <td class="attendance__table-data">
          @if($attendance->created_at != $attendance->updated_at)
          {{ $formattedWorkTimes[$attendance->id] ?? '' }}
          @else
          {{ '' }}
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $attendances->links() }}
</div>
@endsection
