<?php
    require_once './helpers/MemberDAO.php';

    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
    
    $member = null;
    if(isset($_SESSION['member'])){
        $member = $_SESSION['member'];
    }
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="top.php">
            <img src="images/ロゴ.png" alt="ロゴ" height="50">
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if(isset($member)) : ?>
                    <li class="nav-item"><a href="cart.php" class="nav-link" style="color: black;">カート</a></li>
                    <li class="nav-item"><a href="mypage.php" class="nav-link" style="color: black;">マイページ</a></li>
                    <li class="nav-item"><a href="okiniiri.php" class="nav-link" style="color: black;">お気に入り</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link" style="color: black;">ログアウト</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php" class="nav-link" style="color: black;">ログイン</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>