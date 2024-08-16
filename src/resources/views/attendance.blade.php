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

@section('content')
<div class="attendance__table">
  <form class="date__form" method="GET" action="/change_date">
    @csrf
    <button class="change_date" type="submit" name="change_date" value="previous"><</button>
    {{ $date }}
    <input type="hidden" name="date" value="{{ $date }}">
    <button class="change_date" type="submit" name="change_date" value="next">></button>
  </form>
  <table class="attendance__table-body">
    <thead>
      <tr class="attendance__table-row">
        <th class="attendance__table-header">名前</th>
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
            <a class="user_name" href="{{ route('user.profile', ['user_id' => $attendance->user_id]) }}">
              {{ $attendance->user->name ?? '' }}
            </a>
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
  {{ $attendances->appends(['date' => $date])->links() }}
</div>
@endsection
