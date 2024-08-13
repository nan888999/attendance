@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('title','メール送信完了')

@section('content')
<p>ご本人様確認のため、ご登録いただいたメールアドレスに、本登録のご案内メールが届きます。</p>
<p>そちらに記載されているURLにアクセスし、本登録を完了させてください。</p>
@endsection