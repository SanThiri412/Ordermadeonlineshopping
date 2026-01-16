<?php
    require_once './helpers/MemberDAO.php';
    
    session_start();

    if(!isset($_SESSION['member'])){
        header('Location: login.php');
        exit;
    }

    $member = $_SESSION['member'];  

    if($_SERVER['REQUEST_METHOD']==='POST'){

        $name = $_POST['name'] ?? '';
        $kanaName = $_POST['kanaName'] ?? '';
        $nickname = $_POST['nickName'] ?? '';
        $email = $_POST['email'] ?? '';
        $tel = $_POST['tel'] ;
        $postcode = $_POST['postcode'] ?? '';
        $address = $_POST['address'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $cardnumber = $_POST['cardnumber'] ?? '';
        $self_introduction = $_POST['self_introduction'] ?? '';

        $memberDAO = new memberDAO();
        $errs=[];

        if($email === ''){
            $email = $_SESSION['member']->email;
        }
        if($password === ''){
            $password = $_SESSION['member']->password;
        }
        if($nickname ===''){
            $nickname = $_SESSION['member']->nickName;
        }
        if($name ===''){
            $name = $_SESSION['member']->name;
        }
        if($kanaName ===''){
            $kanaName = $_SESSION['member']->kanaName;
        }
        if($postcode ===''){
            $postcode = $_SESSION['member']->postcode;
        }
        if($address ===''){
            $address = $_SESSION['member']->address;
        }
        if($tel ===''){
            $tel = $_SESSION['member']->tel;
        }
        if($cardnumber ===''){
            $cardnumber = $_SESSION['member']->cardnumber;
        }
        if($self_introduction ===''){
            $self_introduction = $_SESSION['member']->self_introduction;
        }

        // ここ（上書き保存）
        if (isset($_FILES['image']) &&$_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];

            // MIMEタイプを安全に判定
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif'
            ];

            if (!isset($allowed[$mime])) {
                die("画像形式は JPG / PNG / GIF だけ使えます。");
            }

            $uploadDir = "images/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // ★ログイン中会員IDで固定ファイル名
            $memberId = $_SESSION['member']->member_id;
            $newName = "0".$memberId . '.' . $allowed[$mime];
            $savePath = $uploadDir . $newName;

            // move_uploaded_file は同名なら自動で上書き
            if (!move_uploaded_file($file['tmp_name'], $savePath)) {
                die("画像の保存に失敗しました。");
            }

            // DB に保存する画像名
            $member_image = $newName;
            $_SESSION['member']->member_image = $newName;
        }

            $member = new member();
            $member->name=$name;
            $member->kanaName=$kanaName;
            $member->nickName=$nickname;
            $member->cardnumber=$cardnumber;
            $member->email = $email;
            $member->password = $password;
            $member->postcode=$postcode;
            $member->address=$address;
            $member->self_introduction=$self_introduction;
            $member->tel=$tel;
            $member->member_id = $_SESSION['member']->member_id;
            
            $memberDAO->update($member);
            $_SESSION['member'] = $member;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員情報変更</title>
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
</head>
<body>
    <?php include "header.php" ?>
    <form action="member_henkou.php" method="post" enctype="multipart/form-data">
    <div class="container mt-4 p-4 w-50 border rounded">
        <div class="hero-section text-center">
            <div class="container">
                <h2 class="text-center mb-5">会員情報変更</h2>
            </div>
        </div>
            <div class="mb-3">
                <label for="exampleInuputEmail" class="form-label">メールアドレス：</label>
                <input type="email" name="email" class="form-control" value="<?= $member->email ?>">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword" class="form-label">パスワード：</label>
                <input type="password" name="password" class="form-control" placeholder="パスワードを入力してください"> 
            </div>
            
            <div class="mb-3">
                <label for="exampleInputPassword" class="form-label">パスワード再入力：</label>
                <input type="password" name="password2" class="form-control" placeholder="再度パスワードを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputName" class="form-label">ニックネーム：</label>
                <input type="name" name="nickName" class="form-control" value=<?= $member->nickName ?> placeholder="ニックネームを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputName" class="form-label">名前：</label>
                <input type="name" name="name" class="form-control" value=<?= $member->name ?> placeholder="お名前を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputName" class="form-label">フリガナ：</label>
                <input type="name" name="kanaName" class="form-control" value=<?= $member->kanaName ?> placeholder="お名前のフリガナをカタカナで入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">郵便番号：</label>
                <div class="row">
                    <div class="col-5"><input type="text" name="postcode" class="form-control" value=<?= $member->postcode ?> placeholder="xxx-xxxx"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">住所：</label>
                <input type="text" name="address" class="form-control" id="exampleInputtext" value=<?= $member->address ?> placeholder="住所を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">電話番号：</label>
                <div class="row">
                    <div><input type="text" name="tel" class="form-control" value=<?= $member->tel ?> placeholder="xxx-xxxx-xxxx">
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">登録済みカード情報：</label>
                <input type="text" name="cardnumber" class="form-control" id="exampleInputtext" value=<?= $member->cardnumber ?> placeholder="カード情報を入力してください">
            </div>
            <div class="mb-3">
                <label class="form-label">自己紹介</label>
                <textarea rows="4" class="form-control" name="self_introduction"><?= 
                    htmlspecialchars($member->self_introduction ?? '', ENT_QUOTES, 'UTF-8') 
                ?></textarea>
            </div>
            <div class="mb-3 text-center">
                <input type="file" class="form-control mb-2" name="image" id="imageInput" accept="image/*">
                <img id="preview" width="200" src="<?= isset($member->member_image) ? 'images/' . $member->member_image : '' ?>">
                <script>
                    document.getElementById("imageInput").addEventListener("change", e => {
                        const file = e.target.files[0];
                        document.getElementById("preview").src = URL.createObjectURL(file);
                    });
                </script>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="container btn btn-primary">変更</button>
            </div>
    </div>
</div>
</form>
</body>
</html>