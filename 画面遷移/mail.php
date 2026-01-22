
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link href="css/mail.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メールフォーム画面</title>
     <link href="css/style.css" rel="stylesheet" type="text/css">
     <link href="css/background.css" rel="stylesheet">
     <link href="css/mail.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
     <?php include "header.php"; ?>
     <<div class="container mt-4 p-4 w-50 border rounded">
        <div class="hero-section text-center">
            <div class="container">
                
            </div>


        </div>
        
                         
            <div class="product-image-box">
                
               <img src="images/作家１(ゴールド)/0013.jpg" alt="" width="120px" height="120px" style="padding: 10px; margin: bottom 10px; border: 1px solid #000000; border-radius: 10px;">
            
            </div>

            <div class="product-details">
                <div class="form-group">
                    <label for="product_name">商品名</label>
                    <input type="name" class="form-control" area-describedby="emailHelp" placeholder="name"disabled>
                </div>
                <div class="form-group">
                    <label for="product_description">商品説明</label>
                   <input type="name" class="form-control" area-describedby="emailHelp" placeholder="Explanation"disabled>
                </div>
           
        
        </div>

        <div class="request-section-wrapper">
            <label for="request_text" class="request-label">要望</label>
           <textarea 
        class="form-control" 
        id="requestArea" 
        aria-describedby="emailHelp" 
        placeholder="Request"
        rows="5" ></textarea>
        </div>

        <div class="d-grid gap-2 col-6 mx-auto">
                <a href="rireki.php"><button type="submit" class="container btn btn-primary">送信</button></a>
            </div>
        </div>
</div>

    </form>
</body>
</html>




