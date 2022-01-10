<?
session_start();
$_SESSION['facebook_token'] = $_POST['authResponse']['accessToken'];
?>
