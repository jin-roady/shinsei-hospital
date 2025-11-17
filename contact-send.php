<?php
// 文字コード設定
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// セッション開始（CSRFチェック用）
session_start();

// ======================================
// 1. CSRF トークンチェック
// ======================================
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    http_response_code(400);
    exit('不正なアクセスです。ブラウザを閉じて再度お試しください。');
}
// 使い捨てトークン
unset($_SESSION['csrf_token']);

// ======================================
// 2. POST データ（ホワイトリスト制御）
// ======================================
$allowed_keys = [
    'csrf_token',
    'name',
    'furigana',
    'gender',
    'age',
    'email',
    'emailConfirm',
    'phone1',
    'phone2',
    'phone3',
    'inquiry'
];

foreach ($_POST as $key => $value) {
    if (!in_array($key, $allowed_keys, true)) {
        http_response_code(400);
        exit('不正なリクエストです。');
    }
}

function post($key)
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

$name       = post('name');
$furigana   = post('furigana');
$gender     = post('gender');
$age        = post('age');
$email      = post('email');
$email2     = post('emailConfirm');
$phone1     = post('phone1');
$phone2     = post('phone2');
$phone3     = post('phone3');
$inquiry    = post('inquiry');

// 電話番号まとめ
$phone = '';
if ($phone1 || $phone2 || $phone3) {
    $phone = $phone1 . '-' . $phone2 . '-' . $phone3;
}

// ======================================
// 3. 簡易ヘッダインジェクション対策
// ======================================
function header_injection_safe($value)
{
    return str_replace(["\r", "\n", "%0A", "%0D"], '', $value);
}

$name   = header_injection_safe($name);
$email  = header_injection_safe($email);
$email2 = header_injection_safe($email2);
$phone  = header_injection_safe($phone);

// ======================================
// 4. バリデーション
// ======================================
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

// メール形式チェック
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'メールアドレスの形式が正しくありません。';
}

// メールが2つとも入力されていれば一致チェック
if ($email !== '' && $email2 !== '' && $email !== $email2) {
    $errors[] = 'メールアドレス（確認用）が一致しません。';
}

// 電話番号形式（例：000-000-0000 程度に制限）
if ($phone !== '' && !preg_match('/^\d{2,5}-\d{1,4}-\d{3,4}$/', $phone)) {
    $errors[] = '電話番号の形式が正しくありません。';
}

// 年齢（任意）数字のみ
if ($age !== '' && !ctype_digit($age)) {
    $errors[] = '年齢は数字でご入力ください。';
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

// ======================================
// 5. 管理者宛てメール作成・送信
// ======================================

// ★★ ここを実際の受信アドレスに変更してください ★★
$to = 'koshin@jp-roady.net';

// 件名
$subject = '【信生病院HP】お問い合わせがありました';

// 本文（テキストメール）
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

// ======================================
// 6. 完了画面（サンクスページ）
// ======================================
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