<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        return $this->getAttendanceView($date);
    }

    public function changeDate(Request $request)
    {
        $date = $request->input('date');

        if ($request->has('change_date')) {
            $direction = $request->input('change_date');
            $date = strtotime($date);

            if ($direction == 'previous') {
                    $date = strtotime("-1 day", $date);
            } elseif ($direction == 'next') {
                    $date = strtotime('+1 day', $date);
            }
        $date = date('Y-m-d', $date);
    }
        return $this->getAttendanceView($date);
    }

    private function getAttendanceView($date)
    {
        $attendances = Attendance::with('user')->whereDate('created_at', $date)->paginate(5);

        // 休憩時間計算
        $breaks = BreakTime::with('attendance')->whereDate('created_at', $date)->get();
        $attendance_break_times = [];
        foreach($breaks as $break) {
            if(!is_null($break->updated_at)) {
                $attendance_id = $break->attendance_id;
                $break_time = $break->created_at->diffInSeconds($break->updated_at);
                if(!isset($attendance_break_times[$attendance_id])) {
                    $attendance_break_times[$attendance_id] = 0;
                }
                $attendance_break_times[$attendance_id] += $break_time;
            }
        }
        $formatted_break_times = [];
        foreach($attendance_break_times as $attendance_id => $total_break_seconds) {
            $break_hours = floor($total_break_seconds / 3600);
            $break_minutes = floor(($total_break_seconds % 3600)/ 60);
            $break_seconds = $total_break_seconds % 60;
            $formatted_break_times[$attendance_id] = sprintf('%02d:%02d:%02d', $break_hours, $break_minutes, $break_seconds);
        }

        // 勤務時間計算
        $formatted_work_times = [];
        foreach($attendances as $attendance){
            $user_id = $attendance->user_id;
            $user_work_seconds = $attendance->created_at->diffInSeconds($attendance->updated_at);
            if(isset($attendance_break_times[$attendance->id])){
                $user_work_seconds -= $attendance_break_times[$attendance->id];
            }
        $work_hours = floor($user_work_seconds / 3600);
        $work_minutes = floor(($user_work_seconds %3600)/ 60);
        $work_seconds = $user_work_seconds % 60;
        $formatted_work_times[$attendance->id] = sprintf('%02d:%02d:%02d', $work_hours, $work_minutes, $work_seconds);
        }
        return view ('attendance', compact('attendances', 'date', 'breaks', 'formatted_break_times', 'formatted_work_times'));
    }

    public function workStart(Request $request)
    {
        $user_id = Auth::id();

        Attendance::create([
            'user_id' => $user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/');
    }

    public function workEnd(Request $request)
    {
        $user_id = Auth::id();

        Attendance::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->update(['updated_at' => now()]);

        return redirect ('/');
    }

    public function breakStart(Request $request)
    {
        $user_id = Auth::id();
        $attendance = Attendance::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->first();
        if($attendance){
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return redirect('/');
    }

    public function breakEnd(Request $request)
    {
        $user_id = Auth::id();
        $attendance = Attendance::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->first();
        if($attendance){
            BreakTime::where('attendance_id', $attendance->id)->whereDate('created_at', Carbon::today())->update(['updated_at' => now()]);
        }
        return redirect('/');
    }

    // ユーザー別勤怠表示
    public function showProfile($user_id)
    {
        $user = User::where('id', $user_id)->first();
        $attendances = Attendance::where('user_id',$user_id)->paginate(10);

        // 休憩時間計算
        $attendance_break_times = [];
        foreach($attendances as $attendance) {
            $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
            if($breaks) {
                foreach($breaks as $break){
                    $break_time = $break->created_at->diffInSeconds($break->updated_at);
                    if(!isset($attendance_break_times[$attendance->id])) {
                        $attendance_break_times[$attendance->id] = 0;
                    }
                    $attendance_break_times[$attendance->id] += $break_time;
                }
            }
        }
        $formatted_break_times = [];
        foreach($attendance_break_times as $attendance_id => $total_break_seconds) {
            $break_hours = floor($total_break_seconds / 3600);
            $break_minutes = floor(($total_break_seconds %3600)/ 60);
            $break_seconds = $total_break_seconds % 60;
            $formatted_break_times[$attendance->id] = sprintf('%02d:%02d:%02d', $break_hours, $break_minutes, $break_seconds);
        }

        // 勤務時間計算
        $formatted_work_times = [];
        foreach($attendances as $attendance){
            $user_work_seconds = $attendance->created_at->diffInSeconds($attendance->updated_at);
            if(isset($attendance_break_times[$attendance->id])){
                $user_work_seconds -= $attendance_break_times[$attendance->id];
            }
        $work_hours = floor($user_work_seconds / 3600);
        $work_minutes = floor(($user_work_seconds % 3600)/ 60);
        $work_seconds = $user_work_seconds % 60;
        $formatted_work_times[$attendance->id] = sprintf('%02d:%02d:%02d', $work_hours, $work_minutes, $work_seconds);
        }

        return view ('user_profile', compact('user', 'attendances', 'formatted_break_times', 'formatted_work_times'));
    }
}
