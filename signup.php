<?php
    require_once 'helpers/MemberDAO.php';
    
    if($_SERVER['REQUEST_METHOD']==='POST'){

        $name = $_POST['name'] ?? '';
        $kanaName = $_POST['kanaName'] ?? '';
        $nickname = $_POST['nickname'] ?? '';
        $email = $_POST['email'] ?? '';
        $tel1 = $_POST['tel1'] ?? '';
        $tel2 = $_POST['tel2'] ?? '';
        $tel3 = $_POST['tel3'] ?? '';
        $postcode = $_POST['postcode'] ?? '';
        $address = $_POST['address'] ?? '';
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $cardnumber = $_POST['cardnumber'] ?? '';
        $self_introduction = $_POST['self_introduction'] ?? '';

        $memberDAO = new memberDAO();

        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $errs['email']='メールアドレスの形式が正しくありません。';
        }else if($memberDAO->email_exists($email)){
            $errs['email']='このメールアドレスはすでに登録されています。';
        }
        if(!preg_match('/\A.{4,}\z/',$password)){
            $errs['password']='パスワードは４文字以上で入力してください。';
        }else if($password!==$password2){
            $errs['password']='パスワードが一致しません。';
        }
        if(empty($name)){
            $errs['name']='お名前を入力してください。';
        }
        if(!preg_match('/^\d{3}-\d{4}$/',$postcode)){
            $errs['zipcode']='郵便番号は「123-4567」のように入力してください。';
        }
        if(empty($address)){
            $errs['address']='住所を入力してください。';
        }
        if(!preg_match('/\A(\d{2,5})?\z/',$tel1)||
        !preg_match('/\A(\d{1,4})?\z/',$tel2)||
        !preg_match('/\A(\d{4})?\z/',$tel3)){
            $errs['tel']='電話番号は半角数字2～5桁、1～4桁、4桁で入力してください。';
        }
        if(empty($tel1)||empty($tel2)||empty($tel3)){
            $errs['tel']='電話番号を入力してくだい。';
        }
        //ここ 
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $memberDAO = new memberDAO();
                $member_image = (int)$memberDAO->images_number();
                $member_image++;

                $file = $_FILES['image'];
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file['type'], $allowed)) {
                    die("画像形式は JPG / PNG / GIF だけ使えます。");
                }

                $uploadDir =  "images/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newName = '0'.$member_image . '.' . 'jpg';
                $savePath =$uploadDir .$newName;
                if (move_uploaded_file($file['tmp_name'], $savePath)) {
                    $member_image = $newName;
                } else {
                    die("画像の保存に失敗しました。");
                }
        }else{
                $nullimage="images/Icons/user.png";
                $memberDAO = new memberDAO();
                $member_image = (int)$memberDAO->images_number();
                $member_image++;

                $uploadDir =  "images/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newName = '0'.$member_image . '.' . 'jpg';
                $savePath =$uploadDir .$newName;

                if (copy($nullimage, $savePath)) {
                    $member_image = $newName;
                } else {
                    die("画像の保存に失敗しました。");
                }
        }
         //ここ
        if(empty($errs)){
            $member = new member();
            $member->name=$name;
            $member->kanaName=$kanaName;
            $member->nickName=$nickname;
            $member->cardnumber=$cardnumber;
            $member->email = $email;
            $member->password = $password;
            $member->postcode=$postcode;
            $member->address=$address;
            $member->member_image=$member_image;
            $member->self_introduction=$self_introduction;
            $member->tel='';
            if($tel1 !==''&&$tel2 !==''&&$tel3 !==''){
                $member->tel="{$tel1}-{$tel2}-{$tel3}";
            }
            
            $memberDAO->insert($member);
            header('Location:login.php');
            exit;
            
        }
    }
?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/background.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>新規会員登録</title>
  
</head>
<body>
    <?php include "header.php"; ?>
    <div class="container mt-4 p-4 w-50 border rounded">
        <form action="signup.php" method="POST" enctype="multipart/form-data" class="form-horizontal">

            <h2 class="text-center md-5">新規会員登録</h2>
                <div class="mb-3">
                    メールアドレス*
                    <input type="email" class="form-control" title="メールアドレスに「＠」を挿入してください"name="email" value="<?= @$email ?>" placeholder="例)test01@jec.ac.jp" required>
                    <span style="color:red"><?= @$errs['email'] ?></span>
                </div>
                <div class="mb-3">
                    お名前*
                    <input type="text" class="form-control" name="name" value="<?= @$name ?>"placeholder="例)電子太郎" required>
                    <span style="color:red"><?= @$errs['name'] ?></span>
                </div>
                <div class="mb-3">
                    フリガナ*
                    <input type="text"  class="form-control" name="kanaName" value="<?= @$kanaName ?>"placeholder="例)でんしたろう" required>
                    <span style="color:red"><?= @$errs['kanaName'] ?></span>
                </div>
                <div class="mb-3">
                    ニックネーム*
                    <input type="text"  class="form-control" name="nickname" value="<?= @$nickname ?>" required>
                    <span style="color:red"><?= @$errs['nickname'] ?></span>
                </div>
                <div class="mb-3">
                    パスワード*
                    <input type="password"  class="form-control" minlength="4" name="password" value="<?= @$password ?>" required>
                    <span style="color:red"><?= @$errs['password'] ?></span>
                </div>
                <div class="mb-3">
                    パスワード(再入力)*
                    <input type="password"  class="form-control" name="password2" value="<?= @$password2 ?>">
                </div>
                <div class="mb-3">
                    電話番号*
                    <div class="row">
                    <div class="col-2"><input type="text" class="form-control" name="tel1" placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" name="tel2" placeholder="xxx"></div>-
                    <div class="col-2"><input type="text" class="form-control" name="tel3" placeholder="xxx"></div>
                    <span style="color:red"><?= @$errs['tel'] ?></span>
                    </div>
                </div>
                <div class="mb-3">
                    郵便番号*
                    <input type="text"  class="form-control" pattern="\d{3}-\d{4}" title="郵便番号は3桁-4桁でハイフン(-)を入れて入力してください"name="postcode" value="<?= @$zipcode ?>"placeholder="例)123-4567">
                    <span style="color:red"><?= @$errs['zipcode'] ?></span>
                </div>             
                <div class="mb-3">
                    住所*
                    <input type="text" class="form-control" name="address" value="<?= @$address ?>"placeholder="例)新宿○○">
                    <span style="color:red"><?= @$errs['address'] ?></span>
                </div>
                <div class="mb-3">
                    カード番号*
                    <input type="text" class="form-control" title="カード番号を入力"name="cardnumber" value="<?= @$cardnumber ?>"  required>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">自己紹介</label>
                    <textarea name="self_introduction"><?= @$self_introduction ?></textarea>                    
                </div>
                <div class="d-grid gap-2 col-5 mx-auto">
                    
                    <input type="file" class="container btn btn-primary btn-sm"  name="image"id="imageInput" accept="image/*">
                    <img  id="preview" src="images/icons/user.png" width="100">
                        <script>
                            document.getElementById("imageInput").addEventListener("change", e => {
                                const file = e.target.files[0];
                                if (file) {
                                    document.getElementById("preview").src = URL.createObjectURL(file);
                                }
                            });
                        </script>
             <input type="submit" class="container btn btn-primary btn-sm" value="新規会員登録">
             </div>
            </div>
            </div> 
        </form>
        </div>
</body>
</html>