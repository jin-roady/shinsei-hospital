<?php
// 文字コード設定
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// ====== 1. POSTデータ取得 ======
$name       = isset($_POST['name'])        ? trim($_POST['name'])        : '';
$furigana   = isset($_POST['furigana'])    ? trim($_POST['furigana'])    : '';
$gender     = isset($_POST['gender'])      ? trim($_POST['gender'])      : '';
$age        = isset($_POST['age'])         ? trim($_POST['age'])         : '';
$email      = isset($_POST['email'])       ? trim($_POST['email'])       : '';
$email2     = isset($_POST['emailConfirm']) ? trim($_POST['emailConfirm']) : '';
$phone1     = isset($_POST['phone1'])      ? trim($_POST['phone1'])      : '';
$phone2     = isset($_POST['phone2'])      ? trim($_POST['phone2'])      : '';
$phone3     = isset($_POST['phone3'])      ? trim($_POST['phone3'])      : '';
$inquiry    = isset($_POST['inquiry'])     ? trim($_POST['inquiry'])     : '';

// 電話番号まとめ
$phone = '';
if ($phone1 || $phone2 || $phone3) {
    $phone = $phone1 . '-' . $phone2 . '-' . $phone3;
}

// ====== 2. バリデーション ======
$errors = [];

if ($name === '') {
    $errors[] = 'お名前は必須項目です。';
}
if ($furigana === '') {
    $errors[] = 'フリガナは必須項目です。';
}

// メール or 電話 どちらかは必須
if ($email === '' && $phone === '') {
    $errors[] = 'メールアドレスまたは電話番号のいずれかをご入力ください。';
}

// メールが2つとも入力されていれば一致チェック
if ($email !== '' && $email2 !== '' && $email !== $email2) {
    $errors[] = 'メールアドレス（確認用）が一致しません。';
}

// エラーがある場合はメッセージ表示
if (!empty($errors)) {
?>
    <!DOCTYPE html>
    <html lang="ja">

    <head>
        <meta charset="UTF-8">
        <title>お問い合わせエラー｜信生病院</title>
        <link rel="stylesheet" href="css/tokens.css">
        <link rel="stylesheet" href="css/common.css">
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/pagetop.css">
        <link rel="stylesheet" href="css/contact.css">
    </head>

    <body id="top">
        <header class="hero hero--guide">
            <div class="kv"><span class="kv__label">お問い合わせ</span></div>
        </header>

        <main class="min-h-screen bg-white">
            <section class="section">
                <div class="container">
                    <div class="sec-head">
                        <h2 class="sec-head__title">入力内容に誤りがあります</h2>
                    </div>
                    <div class="contact-card">
                        <p>恐れ入りますが、以下の内容をご確認のうえブラウザの「戻る」ボタンで前の画面にお戻りください。</p>
                        <ul style="margin-top: 1rem; padding-left: 1.2rem;">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <div id="site-footer"></div>
        <div class="pagetop-fixed">
            <a href="#top" aria-label="ページトップへ">PAGE TOP</a>
        </div>
        <script src="sidebar.js" defer></script>
        <script src="footer.js" defer></script>
    </body>

    </html>
<?php
    exit;
}

// ====== 3. 管理者宛てメール作成 ======

// ★★ ここを実際の受信アドレスに変更してください ★★
$to = 'koshin@jp-roady.net';

// 件名
$subject = '【信生病院HP】お問い合わせがありました';

// 本文
$body  = "ホームページのお問い合わせフォームから、以下の内容で送信されました。\n\n";
$body .= "■お名前\n{$name}\n\n";
$body .= "■フリガナ\n{$furigana}\n\n";
$body .= "■性別\n{$gender}\n\n";
$body .= "■年齢\n{$age}\n\n";
$body .= "■メールアドレス\n{$email}\n\n";
$body .= "■電話番号\n{$phone}\n\n";
$body .= "■お問い合わせ内容\n{$inquiry}\n\n";

// Fromヘッダ（さくらの推奨アドレスなどに合わせて変更）
$fromEmail = 'koshin@jp-roady.net';  // ★★ここも環境に合わせて変更★★
$headers   = "From: " . mb_encode_mimeheader("信生病院ホームページ") . " <{$fromEmail}>\r\n";

// メール送信
$sendResult = mb_send_mail($to, $subject, $body, $headers);

// ====== 4. 完了画面（サンクスページ） ======
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>お問い合わせ完了｜信生病院</title>
    <link rel="stylesheet" href="css/tokens.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/pagetop.css">
    <link rel="stylesheet" href="css/contact.css">
</head>

<body id="top">
    <header class="hero hero--guide">
        <div class="kv"><span class="kv__label">お問い合わせ</span></div>
    </header>

    <main class="min-h-screen bg-white">
        <section class="section">
            <div class="container">
                <div class="sec-head">
                    <h2 class="sec-head__title">お問い合わせありがとうございました</h2>
                </div>
                <div class="contact-card">
                    <?php if ($sendResult): ?>
                        <p>
                            お問い合わせを受け付けました。<br>
                            内容を確認のうえ、担当者よりご連絡させていただきます。
                        </p>
                    <?php else: ?>
                        <p>
                            お問い合わせの送信中にエラーが発生しました。<br>
                            恐れ入りますが、時間をおいて再度お試しいただくか、お電話にてお問い合わせください。
                        </p>
                    <?php endif; ?>

                    <p style="margin-top: 24px;">
                        <a href="index.html" class="form-submit__button" style="text-decoration:none;display:inline-block;">
                            トップページへ戻る
                        </a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <div id="site-footer"></div>
    <div class="pagetop-fixed">
        <a href="#top" aria-label="ページトップへ">PAGE TOP</a>
    </div>
    <script src="sidebar.js" defer></script>
    <script src="footer.js" defer></script>
</body>

</html>