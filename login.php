<?php
    require_once './helpers/MemberDAO.php';
    // Use MemberDAO for authentication
    $memberDAO = new MemberDAO();

    $email = '';
    $errs = [];

    // 1. セッションの開始は必ずフォーム処理の前に置く
    session_start();

    // --- フォーム送信時の処理（PHPロジック） ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $loginMessage = '';
        $messageType = '';

        if (empty($email) || empty($password)) {
            $loginMessage = 'メールアドレスとパスワードを入力してください。';
            $messageType = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loginMessage = '有効なメールアドレスを入力してください。';
            $messageType = 'danger';
        } else {
            // データベースからメンバー情報を取得
            $member = $memberDAO->get_member($email, $password); 
            
            if ($member !== false) {
                // ログイン成功
                $_SESSION['member'] = $member; // memberオブジェクト全体
                $_SESSION['user_name'] = $member->name;
                $_SESSION['user_image'] = $member->member_image;
                
                // 🌟 最重要修正ポイント: 以前のファイルで定義した固定キーに、メンバーIDを格納する
                // $member->member_id がIDを保持していると仮定します。
                $_SESSION['MEMBER_ID'] = $member->member_id; 
                
                header('Location: top.php'); // Redirect to the appropriate page
                exit;
            } else {
                // ログイン失敗
                $loginMessage = 'メールアドレスまたはパスワードが間違っています。';
                $messageType = 'danger';
            }
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
    <title>ログイン画面</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "header.php"; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center login-wrapper">
            <div class="col-md-8 col-lg-6">
                <div class="card main-card">
                    <div class="card-header">
                       <img src="images/ロゴ.png" alt="ロゴ" height="50">
                    </div>
                    <div class="card-body">
                        <div class="form-container">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <?php if (!empty($loginMessage)): ?>
                                    <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                                        <?php echo $loginMessage; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label for="email" class="form-label">メールアドレス</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" value="<?php echo isset($email) ? $email : ''; ?>">
                                </div>
                                <div class="d-grid">      
                                 <div class="mb-4 position-relative">
                                    <label for="password" class="form-label">パスワード</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    <span class="toggle-password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </span>
                                </div>

                                    <button type="submit" class="btn-primary">ログイン</button>
                                </div>
                            </form>
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

<script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
