# 勤怠管理システムAtte
勤怠管理システムです。
![image](https://github.com/user-attachments/assets/8d8db124-c006-47ab-818a-6ae3be917584)

## 作成した目的
企業の勤怠管理をするために作成しました。

## アプリケーションURL
- 開発環境：http://localhost/
- phpMyAdmin : http://localhost:8080/

## 機能一覧
- メールアドレス認証・会員登録
- ログイン
- ログアウト
- 勤務開始（1日1回のみ打刻可能）
- 勤務終了
- 休憩開始（1日に何度も休憩可能）
- 休憩終了
- 日付別勤怠情報取得
- ユーザー別勤務状況取得
- ページネーション
- スケジューラー
  - 「勤務開始」後その日中に打刻がなければ、翌0:00に自動で「勤務終了」
  - 認証メールアドレス送信後、60分以内に会員登録されなければusersテーブルよりユーザーを自動削除

## 使用技術
- PHP 7.4.9
- Laravel Framework 8.83.8
- MySQL  8.0.26

## テーブル設計
<img width="667" alt="テーブル仕様書_attendance" src="https://github.com/user-attachments/assets/b2c7f23e-5276-44dd-b3a6-b739df996e89">

## ER図
<img width="709" alt="ER図_attendance" src="https://github.com/user-attachments/assets/e31839d2-03f7-4064-a1de-fb33cce9912f">

## 環境構築
Dockerビルド
1. git clone リンク
2. docker-compose up -d —build

Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更
4. php artisan:key generate
5. php artisan migrate
6. php artisan schedule:work（スケジューラー起動）

## テストユーザー
メールアドレス「test@test」
パスワード「testtesttest」
