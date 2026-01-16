<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// セッション変数を空に
$_SESSION = [];

// クッキー削除（セッション名を取得して確実に削除）
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// セッション破棄
session_destroy();

// トップへリダイレクト（top.php に変更したいならここを 'top.php' に）
header('Location: top.php');
exit;