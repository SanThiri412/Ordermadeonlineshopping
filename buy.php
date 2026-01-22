<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
    <title>購入</title>
</head>
<body>
    <style>
        .image a:hover{
            opacity: .6;
        }
    </style>
    <?php include "header.php" ?>
    <form action="top.php" method="post">
    <div class="container mt-4 p-4 w-50 border rounded">
    <div class="container card-section">
        <h2 class="text-center mb-5">購入確認</h2>
        <div class="row">
            <div class="col-md-6 mb-6">
                〇配送先　　
                <div class="mb-3 form-check form-check-inline">
                    <input class="form-check-input load07" type="radio" name="code" id="radioOff" value="off">
                    <label class="form-check label" for="radioOff">会員情報と同じ住所に配送する</label>
                </div>
                <div class="mb-3">
                    <label for="exampleInuputName" class="form-label">お名前：</label>
                    <input type="name" class="form-control" area-describedby="emailHelp" placeholder="name@example.com">
                </div>
                <div class="mb-3">
                    <label for="exampleInputEmail" class="form-label">フリガナ：</label>
                    <input type="email" class="form-control"
                    placeholder="パスワードを入力してください">
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
                    <label for="exampleInputtext" class="form-label">郵便番号：</label>
                    <div class="row">
                        <div class="col-2"><input type="text" class="form-control " placeholder="xxx"></div>-
                        <div class="col-2"><input type="text" class="form-control" placeholder="xxxx"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="exampleInputtext" class="form-label">住所：</label>
                    <input type="text" class="form-control" placeholder="お名前を入力してください">
                </div>
                <br>
                〇支払い　　
                <div class="mb-3 form-check form-check-inline">
                    <input class="form-check-input load07" type="radio" name="code" id="radioOff" value="off">
                    <label class="form-check label" for="radioOff">会員情報と同じカード番号を使用する</label>
                </div>
                <div class="mb-3">
                    <label for="exampleInputtext" class="form-label">登録済みカード情報：</label>
                    <input type="text" class="form-control" id="exampleInputtext" placeholder="カード情報を入力してください">
                </div>
            </div>
            <div class="col-md-6 mb-6 card h-100 shadow-sm">
                <div class="overflow-auto" style="max-height: 600px;">
                <table>
                     <tr>
                        <td rowspan="5">
                            <figure class="image"><a href="goods.php">
                                <img src="images/作家８(和風)/0087.jpg" height="120px" style="padding: 10px; margin: bottom 10px;">
                            </a></figure>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">
                            和風の腕輪です
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                                購入個数：　１　
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            単価　　：10,000　
                        </td>
                    </tr>
                </table>
                </div>
                <br><hr><br>
                <div style="text-align: right;">
                    合計購入点数：　１　<br>
                    総合計金額　：　10,000　
                </div>
                <br>
            </div>
        </div>
    </div>
    <br>
    <div class="d-grid gap-2 col-6 mx-auto">
        <button type="submit" class="container btn btn-primary">購入確定</button>
    </div>
    </div>
</form>
</body>
</html>