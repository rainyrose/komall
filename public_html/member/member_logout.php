<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>홈페이지 통합관리자</title>
</head>
<body>
		<?
			session_start();
			@session_destroy();
			echo "<script type='text/javascript'>alert('로그아웃되었습니다.');</script>";
			echo "<script type='text/javascript'> parent.location.href='/".$add_folder."'; </script>";
		?>
	</body>
</html>
