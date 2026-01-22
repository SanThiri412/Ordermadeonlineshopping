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
    <form action="mypage.php" method="post">
    <div class="container mt-4 p-4 w-50 border rounded">
        <div class="hero-section text-center">
            <div class="container">
                <h2 class="text-center mb-5">会員情報変更</h2>
            </div>
        </div>
    
        <form>
            <div class="mb-3">
                <label for="exampleInuputName" class="form-label">メールアドレス：</label>
                <input type="name" class="form-control" area-describedby="emailHelp" placeholder="name@example.com">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail" class="form-label">パスワード：</label>
                <input type="email" class="form-control"
                 placeholder="パスワードを入力してください">
                </div>
            
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">パスワード再入力：</label>
                <input type="text" class="form-control" placeholder="再度パスワードを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputText" class="form-label">ニックネーム：</label>
                <input type="text" class="form-control" placeholder="ニックネームを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">名前：</label>
                <input type="text" class="form-control" placeholder="お名前を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">フリガナ：</label>
                <input type="text" class="form-control" placeholder="お名前のフリガナをカタカナで入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">郵便番号：</label>
                <div class="row">
                    <div class="col-2"><input type="text" class="form-control " placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" placeholder="xxxx"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">住所：</label>
                <input type="text" class="form-control" id="exampleInputtext" placeholder="住所を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">電話番号：</label>
                <div class="row">
                    <div class="col-2"><input type="text" class="form-control " placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" placeholder="xxxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" placeholder="xxxx"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">登録済みカード情報：</label>
                <input type="text" class="form-control" id="exampleInputtext" placeholder="カード情報を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">自己紹介</label>
                <textarea rows="4" cols="40" class="form-control" placeholder="自己紹介を入力してください"></textarea>
            </div>
            <div class="mb-3">
                <div class="rowspan-4">
                    <label for="exampleInputtext" class="form-label">アイコン画像：</label>
                </div>
                <img src="images/Icons/user.png" alt="" width="120px" height="120px" style="padding: 10px; margin: bottom 10px; border: 1px solid #000000; border-radius: 10px;">
                <button type="submit" class="btn btn-primary btn-sm">画像変更</button>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="container btn btn-primary">変更</button>
            </div>
        </form>
    </div>
</div>
</form>
</body>
</html>