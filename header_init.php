<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/helpers/MemberDAO.php';

$member = null;
if (isset($_SESSION['member'])) {
    $member = $_SESSION['member'];
}