<?
if(!$_SESSION['auth_level']) $_SESSION['auth_level'] = 10;

$auth_write = true;
$auth_read = true;
$auth_delete = true;
$auth_comment = true;
$auth_reply = true;

//직급별 권한
/*
if($program[auth_write] < $_SESSION['auth_level']) $auth_write = false;
if($program[auth_read] < $_SESSION['auth_level']) $auth_read = false;
if($program[auth_delete] < $_SESSION['auth_level']) $auth_delete = false;
if($program[auth_comment] < $_SESSION['auth_level']) $auth_comment = false;
if($program[auth_reply] < $_SESSION['auth_level']) $auth_reply = false;
*/


//부서별 권한
if($program[dept_auth]){
	if(!strpos($program[dept_auth], $_SESSION[member_dept])){
		error("해당 프로그램의 접근 권한이 없습니다.");
		exit;
	}
}
?>
