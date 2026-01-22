<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品出品</title>
    <link rel ="shohinshupin.css" href="css/shohinshupin.css">
    <link rel="stylesheet" href="bootstrap-5.0.0-dist/css/bootstrap.min.css">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/title.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "header.php" ?>
<div class="container mt-4 p-4 w-50 border rounded">
        <div class="hero-section text-center">
            <div class="container">
                
            </div>


        </div>
    
        <form>
            <div class="mb-3">
                
                <img src="images/作家１(ゴールド)/0006.jpg" alt="" width="120px" height="120px"class="product-icon">
                

            </div>
            <div class="mb-3">
                <label for="exampleInuputName" class="form-label">商品名：</label>
                <input type="name" class="form-control" area-describedby="emailHelp" placeholder="Name">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail" class="form-label">商品分類：</label>
                <input type="text" class="form-control"
                 placeholder="Classification">
                </div>
            
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">ジャンル：</label>
                <input type="text" class="form-control" placeholder="Genre">
            </div>
            <div class="mb-3">
                <label for="exampleInputText" class="form-label">商品情報：</label>
                <input type="text" class="form-control" placeholder="Information">
            </div>
            <div class="mb-3">
                <label for="exampleInputtext" class="form-label">値段：</label>
                <input type="text" class="form-control" placeholder="Price">
            </div>
            
            おすすめ：　　　
            <div class="mb-3 form-check form-check-inline">
                <input class="form-check-input load07" type="checkbox" name="code" id="checkOff" value="off">
                <label class="form-check label" for="radioOff">おすすめに設定する</label>
            </div>
              <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="container btn btn-primary">出品</button>
            </div>
        </form>
    </div>
</div>
</div>
</body>
</html>