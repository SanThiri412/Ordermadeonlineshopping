<?php
use  PHPMailer\PHPMailer\PHPMailer;
use  PHPMailer\PHPMailer\Exception;
use  PHPMailer\PHPMailer\SMTP;

require('./PHPMailer/PHPMailer/src/PHPMailer.php');
require('./PHPMailer/PHPMailer/src/Exception.php');
require('./PHPMailer/PHPMailer/src/SMTP.php');

require_once 'helpers/MemberDAO.php';
require_once 'helpers/HistoryDAO.php';

session_start();

if (!isset($_SESSION['member'])) {
    header('Location: login.php');
    exit;
}
$member = $_SESSION['member'];
$current_member_id = $member->member_id;

$HistoryDAO = new HistoryDAO();
$MemberDAO  = new MemberDAO();

$goodsCode = $_REQUEST['goodsCode'] ?? $_REQUEST['goodscode'] ?? null;
$to_member_id = $_REQUEST['member_id'] ?? null;

// 商品と役割の特定
$History_list = $HistoryDAO->get_buy_by_memberid($current_member_id); 
$Orders_list  = $HistoryDAO->get_buyOrders_by_memberid($current_member_id); 

function find_in_list($list, $code) {
    if (!$list) return null;
    foreach ($list as $item) {
        $val = $item->goodscode ?? $item->goodsCode ?? null;
        if ($val !== null && (string)$val === (string)$code) return $item;
    }
    return null;
}

$found = find_in_list($Orders_list, $goodsCode);
$is_seller = ($found !== null); 
if (!$found) $found = find_in_list($History_list, $goodsCode);

if ($found && !$to_member_id) {
    $to_member_id = $is_seller ? ($found->Orders_member_id ?? $found->member_id) : ($found->goods_member_id ?? $found->member_id);
}

// 相手情報取得
$partner = $to_member_id ? $MemberDAO->get_member_by_id((int)$to_member_id) : null;
$partner_nickname = $partner->nickName ?? 'ユーザー';
$partner_email = $partner->email ?? '';
// 表示用
$goodsName = $found->goodsname ?? $found->goodsName ?? '';
$goodsText = $found->goodsText ?? $found->goods_text ?? '';
$imagePath = !empty($found->goods_image) ? 'images/' . ltrim($found->goods_image, '/') : 'images/no_image.png';

$message = '';

// --- PHPMailer 送信処理 ---
if (isset($_POST['send']) && $partner_email) {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'utf-8';
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '24jn0220@jec.ac.jp'; // ★あなたのメアド★
        $mail->Password   = 'lzka uley mquh fhst'; // ★アプリパスワード★
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('24jn0220@jec.ac.jp', 'SELARG事務局'); // 送信元
        $mail->addAddress($partner_email, $partner_nickname . '様');

        // メールの件名と本文
        $mail->Subject = "【SELARG】商品「{$goodsName}」についてのお問い合わせ";

        $mail->Body = "{$partner_nickname} 様\n\n" 
                    . "{$member->nickName} 様よりメッセージが届きました。\n\n"
                    . "下記の内容での制作をお願いいたします。\n"
                    . "内容：\n{$_POST['request']}\n\n"
                    . "--------------------------\n"
                    . "商品：{$goodsName}\n"
                    . "--------------------------\n"
                    . "【SELARG】";

        $mail->send();
        $message = '<div style="color:green; text-align:center; margin-bottom:10px;">メールを送信しました。</div>';
    } catch (Exception $e) {
        $message = '<div style="color:red;">送信に失敗しました。エラー: ' . $mail->ErrorInfo . '</div>';
    }
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '24jn0220@jec.ac.jp'; // ★あなたのメアド★
        $mail->Password   = 'lzka uley mquh fhst'; // ★アプリパスワード★
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('24jn0220@jec.ac.jp', 'SELARG事務局'); // 送信元
        $mail->addAddress($partner_email, $partner_nickname . '様');

        // メールの件名と本文
        $mail->Subject = "【SELARG】商品「{$goodsName}」のお問い合わせについて";

        $mail->Body = "{$member->nickName} 様\n\n" 
                    . "{$partner_nickname} 様よりメッセージが届きました。\n\n"
                    ."この度は作品をご注文いただき、誠にありがとうございました。\n"
                    ."下記の内容で、メールを送信いたしました。\n"
                    . "内容：\n{$_POST['request']}\n\n"
                    . "--------------------------\n"
                    . "商品：{$goodsName}\n"
                    . "--------------------------\n"
                    . "商品がお手元に届くまで、今しばらくお待ちください\n\n"
                    . "【SELARG】";

        $mail->send();
        $message = '<div style="color:green; text-align:center; margin-bottom:10px;">メールを送信しました。</div>';
    } catch (Exception $e) {
        $message = '<div style="color:red;">送信に失敗しました。エラー: ' . $mail->ErrorInfo . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/mail.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <title>SELARG - メール作成</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="container">
        <?= $message ?>
        <div class="img-box">
            <img src="<?= htmlspecialchars($imagePath) ?>">
        </div>

    <form action="" method="post">
        <div class="label">商品名</div>
        <div class="field"><?= htmlspecialchars($goodsName) ?></div>

        <div class="label">商品説明</div>
        <div class="field"><?= htmlspecialchars($goodsText) ?></div>

        <div class="label">要望・メッセージ</div>
        <textarea name="request" required placeholder="ここにメッセージを入力してください"></textarea>

        <input type="submit" name="send" value="送信" class="btn-primary">
    </form>
</div>

</body>
</html>