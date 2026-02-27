<?php
    // DSN: Azure SQL Serverのサーバー名とデータベース名
    define('DSN', 'sqlsrv:server=tcp:selarg-server-0227.database.windows.net,1433;Database=selarg');
    
    // ユーザー名とパスワード
    define('DB_USER', 'jndb');
    define('DB_PASSWORD', 'Pa$$word1234');

    // 接続オプション（文字化け防止とエラー表示設定）
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
    );

    try {
        // 接続テスト用
        $pdo = new PDO(DSN, DB_USER, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        // エラーが出た場合は詳細を表示
        die("データベース接続エラー: " . $e->getMessage());
    }
?>