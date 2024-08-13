<!DOCTYPE html>
<html lang="ja">
<style>
  body {
    margin: 20px auto;
    max-width: 1020px;
  }

  h1 {
    font-size: 16px;
  }
</style>
<body>
  <h1>【Atte】メールアドレス認証</h1>
  <p>会員登録を完了するには、下記のボタンを押し、必要事項を入力してください。</p>
  <p><a href="{{ $verificationUrl }}">会員登録</a></p>
  <p>このリンクは{{ config('auth.verification.expire', 60) }}分で期限切れになります。</p>
  <p>このメールに身に覚えのない場合は無視してください。</p>
</body>
</html>