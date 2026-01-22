<?php
// --- フォーム送信時の処理（PHPロジックは変更なし） ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '';
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '';
    $loginMessage = '';
    $messageType = '';

    if (empty($email) || empty($password)) {
        $loginMessage = 'メールアドレスとパスワードを入力してください。';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginMessage = '有効なメールアドレスを入力してください。';
        $messageType = 'danger';
    } else if ($email === 'user@example.com' && $password === 'password123') {
        $loginMessage = 'ログインに成功しました！';
        $messageType = 'success';
    } else {
        $loginMessage = 'メールアドレスまたはパスワードが間違っています。';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/loginforstyle.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <title>ログイン画面 (画像デザイン再現)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "header2.php"; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center login-wrapper">
            <div class="col-md-8 col-lg-6">
                <div class="card main-card">
                    <div class="card-header">
                       <img src="images/ロゴ.png" alt="ロゴ" height="50">
                    </div>
                    <div class="card-body">
                        
                        <div class="form-container">


                                <div class="mb-3">
                                    <label for="email" class="form-label">メールアドレス</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="<?php echo isset($email) ? $email : ''; ?>">
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">パスワード</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                </div>
                                <a href="mypage.php" class="btn-primary">
                                   <button type="submit" class="btn-primary">ログイン</button>
                                </a>
                          
                        </div>
                        
                        <hr class="form-divider">
                        
                        <div class="register-section">
                             <h6 class="mb-2">初めてご利用の方</h6>
                             <p class="mb-3">ログインするには会員登録が必須です</p>
                             <a href="signup.php" class="btn-register">
                                 <i class="bi bi-check-circle"></i>新規会員登録はこちら
                             </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>