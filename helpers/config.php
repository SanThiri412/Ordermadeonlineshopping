<?php
    // DSNにはサーバー名、ポート、データベース名のみを記述します
    define('DSN', "sqlsrv:server=tcp:24jn0201db.database.windows.net,1433; Database=selarg");
    
    // ユーザー名とパスワードは個別の定数として定義します
    define('DB_USER', 'jndb');
    define('DB_PASSWORD', 'Pa$$word1234');

    // 接続オプション（Azure SQLでは暗号化設定が推奨されます）
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
    );

    try {
        // PDO インスタンスの作成
        $pdo = new PDO(DSN, DB_USER, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        die("接続エラー: " . $e->getMessage());
    }
?>