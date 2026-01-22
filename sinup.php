<?php
?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <title>新規会員登録</title>
</head>
<body>
    <?php include "header2.php"; ?>
    <div class="container mt-4 p-4 w-50 border rounded">
         <form action="login.php" method="POST" class="form-horizpntal">
            <h2 class="text-center md-5">新規会員登録</h2>
                <div class="mb-3">
                    メールアドレス*
                    <input type="email" class="form-control" title="メールアドレスに「＠」を挿入してください"name="email" value="<?= @$email ?>" placeholder="例)test01@jec.ac.jp" required>
                    <span style="color:red"><?= @$errs['email'] ?></span>
                </div>
                <div class="mb-3">
                    お名前*
                    <input type="text" class="form-control" name="name" value="<?= @$membername ?>"placeholder="例)電子太郎" required>
                    <span style="color:red"><?= @$errs['name'] ?></span>
                </div>
                <div class="mb-3">
                    フリガナ*
                    <input type="text"  class="form-control" name="kananame" value="<?= @$membername ?>"placeholder="例)でんしたろう" required>
                    <span style="color:red"><?= @$errs['kananame'] ?></span>
                </div>
                <div class="mb-3">
                    ニックネーム*
                    <input type="text"  class="form-control" name="nickname" value="<?= @$membername ?>"placeholder="例)電子太郎" required>
                    <span style="color:red"><?= @$errs['nickname'] ?></span>
                </div>
                <div class="mb-3">
                    パスワード*
                    <input type="password"  class="form-control" minlength="4" name="password" value="<?= @$password ?>" required>
                    <span style="color:red"><?= @$errs['password'] ?></span>
                </div>
                <div class="mb-3">
                    パスワード(再入力)*
                    <input type="password"  class="form-control" name="password2"value="<?= @$password2 ?>">
                </div>
                <div class="mb-3">
                    電話番号*
                    <div class="row">
                    <div class="col-2"><input type="text" class="form-control" name="tal" placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" placeholder="xxx"></div>
                    <span style="color:red"><?= @$errs[''] ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    郵便番号*
                    <input type="text"  class="form-control" pattern="\d{3}-\d{4}" title="郵便番号は3桁-4桁でハイフン(-)を入れて入力してください"name="zipcode" value="<?= @$zipcode ?>"placeholder="例)123-4567">
                    <span style="color:red"><?= @$errs['zipcode'] ?></span>
                </div>             
                <div class="mb-3">
                    住所*
                    <input type="text" class="form-control" name="address" value="<?= @$address ?>"placeholder="例)新宿○○">
                    <span style="color:red"><?= @$errs['address'] ?></span>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">自己紹介</label>
                    <textarea type="text" class="form-control" placeholder="自己紹介を入力してください" name="self_introduction" value="<?=@$self_introduction ?>"id="exampleFormControlTextarea1" rows="3"></textarea>                    
                </div>
            <div class="d-grid gap-2 col-5 mx-auto">
            <form action="" method="POST" enctype="multipart/form-data">
                   <div class="mb-3">
                    <table>
                        <td rowspan="2">
                        アイコン画像<br>
                        <img src="images/Icons/user.png" alt="サンプル画像が表示されます。" widht="120px"  height="120px" style="padding: 10px; margin-bottom: 10px; border: 1px solid #333333; border-radius: 10px;">
                            <input type="file" name="file" class="btn btn-primary btn-sm"  value="画像変更">
                            <input type="button" class="btn btn-primary btn-sm"value="アップロード">
                        <td>
                    </table>
                </div>
            </form>
             <input type="submit" class="container btn btn-primary btn-sm" value="新規会員登録">
             </div>
            </div>
            </div> 
        </form>
        </div>
</body>
</html>