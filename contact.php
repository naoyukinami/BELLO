<?php
mb_language("Japanese");
mb_internal_encoding("UTF-8");

$to   = "charoguti@icloud.com";      // お申し込みメールを受け取りたいアドレス
$from = "info@bellofootball.club";     // 送信元


$thanks_url = "thanks.html"; 

// POST以外のアクセスは拒否
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ./");
    exit;
}

// スパム対策：ハニーポット欄が埋まっていたら何もせず終了
if (!empty($_POST["website"])) {
    exit;
}

// 入力値を取得し、改行を除去してヘッダーインジェクションを防ぐ
function clean_line($value) {
    $value = trim($value ?? "");
    $value = str_replace(["\r\n", "\r", "\n"], "", $value);
    return $value;
}

$parentName = clean_line($_POST["parent_name"] ?? "");
$childName  = clean_line($_POST["child_name"] ?? "");
$grade      = clean_line($_POST["grade"] ?? "");
$experience = clean_line($_POST["experience"] ?? "");
$email      = clean_line($_POST["email"] ?? "");
$message    = trim($_POST["message"] ?? "");

// 必須項目チェック
if ($parentName === "" || $childName === "" || $email === "") {
    http_response_code(400);
    echo "必須項目が入力されていません。前のページに戻ってご確認ください。";
    exit;
}

// メールアドレスの形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "メールアドレスの形式が正しくありません。前のページに戻ってご確認ください。";
    exit;
}

// メール本文を作成
$subject = "【無料体験申し込み】" . $childName . "さん（" . $parentName . "様）";

$body  = "ウェブサイトより無料体験のお申し込みがありました。\n\n";
$body .= "----------------------------------------\n";
$body .= "保護者のお名前：" . $parentName . "\n";
$body .= "お子さんの名前：" . $childName . "\n";
$body .= "学年：" . ($grade !== "" ? $grade : "未選択") . "\n";
$body .= "サッカー経験：" . ($experience !== "" ? $experience : "未選択") . "\n";
$body .= "メールアドレス：" . $email . "\n";
$body .= "体験希望日・お問い合わせ内容：\n" . ($message !== "" ? $message : "（記入なし）") . "\n";
$body .= "----------------------------------------\n";

$headers  = "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// メール送信
$result = mb_send_mail($to, $subject, $body, $headers);

if ($result) {
    header("Location: " . $thanks_url);
    exit;
} else {
    http_response_code(500);
    echo "送信に失敗しました。お手数ですが、時間をおいて再度お試しください。";
    exit;
}