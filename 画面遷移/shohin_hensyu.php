<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
</head>
<body>
    <?php include "header.php" ?>
    <form action="shuppin_shohinhyoji.php" method="post">
<div class="container mt-4 p-4 w-50 border rounded">
        <div class="hero-section text-center">
            <div class="container">
                <h2 class="text-center mb-5">出品編集</h2>
            </div>
        </div>
    
        <form>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">商品画像：</label>
                <img src="images/Icons/new-product.png" alt="" width="120px" height="120px" style="padding: 10px; margin: bottom 10px; border: 1px solid #000000; border-radius: 10px;">
                <button type="submit" class="btn btn-primary btn-sm">画像変更</button>
            </div>
            <div class="mb-3">
                <label for="exampleInuputName" class="form-label">商品名：</label>
                <input type="name" class="form-control" area-describedby="emailHelp" placeholder="name@example.com">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail" class="form-label">商品分類：</label>
                <input type="email" class="form-control"
                 placeholder="パスワードを入力してください">
                </div>
            
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">ジャンル：</label>
                <input type="text" class="form-control" placeholder="再度パスワードを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputText" class="form-label">値段：</label>
                <input type="text" class="form-control" placeholder="ニックネームを入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">商品詳細：</label>
                <input type="text" class="form-control" placeholder="お名前を入力してください">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">出品数：</label>
                <input type="text" class="form-control" placeholder="お名前のフリガナをカタカナで入力してください">
            </div>
            おすすめ：　　　
            <div class="mb-3 form-check form-check-inline">
                <input class="form-check-input load07" type="checkbox" name="code" id="checkOff" value="off">
                <label class="form-check label" for="radioOff">おすすめに設定する</label>
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