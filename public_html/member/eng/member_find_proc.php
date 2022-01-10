<?
//  ID 정리
$reg_date = date("Y-m-d H:i:s");
$birthday = $birthday1."-".$birthday2."-".$birthday3;

//Forgot your password
if($return_mode == "find_pw"){

	//본인인증
	if($site[use_namecheck] == "Y"){
		//TODO
		$check = mysqli_fetch_array(mysqli_query($connect, "SELECT *  FROM koweb_member WHERE CI='$_SESSION[CI]' AND DI = '$_SESSION[DI]' LIMIT 1"));
		$check_where = "WHERE CI='$_SESSION[CI]' AND DI='$_SESSION[DI]'";

		if (!$check[id])	error("There is no registered membership information.");
		if (!$check[no])	error("There is no registered membership information.");

	} else {
		if (!$find_id)	error("Please enter your ID.");
		if (!$find_name)	error("Input your name, please.");
		if (!$find_email)	error("Please enter your email address.");

		$check = mysqli_fetch_array(mysqli_query($connect, "SELECT *  FROM koweb_member WHERE id='$find_id' AND name='$find_name' AND email='$find_email' LIMIT 1"));
		$check_where = "WHERE id='$find_id' AND name='$find_name' AND email = '$find_email'";

		if (!$check[id])	error("There is no registered membership information.");
		if (!$check[no])	error("There is no registered membership information.");
	}

	//temporary password 생성
	$temp_passwd_word = array("a","b","c","d","e","f","g","h","i","j","k","l","n","m","o","p","q","r","s","t","u","v","w","x","y","z",
						 "A","B","C","D","E","F","G","H","I","J","K","L","N","M","O","P","Q","R","S","T","U","V","W","X","Y","Z",
						 "1","2","3","4","5","6","7","8","9","0",
						 "~","!","@","#","$","%","^","&","*","(",")","_","+","?"
						 );

	for($i = 0; $i < 8; $i++) {
		$rand_num  = array_rand($temp_passwd_word);
		$rand_su .= $temp_passwd_word[$rand_num];
	}

	$temp_password = hash("sha256", $rand_su);
	$temp_token = hash("sha256", $find_id.$rand_su);

	$update = "UPDATE koweb_member SET  password = '$temp_password' $check_where";
	mysqli_query($connect, $update);

	//result
?>
		<div id="find_password">
				<div class="join_area">
					<h2><i>Issue Temporary Password</i></h2>
					<div class="box find">
						<h3>Issue Temporary Password</h3>
						<table class="bbsView">
							<caption>Issue Temporary Password</caption>
							<colgroup>
								<col data-member-form="th" style="width:30%;"/>
								<col data-member-form="td" style="width:70%;"/>
							</colgroup>
							<tbody>
								<tr>
									<th scope="row"><label for="temp_passwd">temporary password</label></th>
									<td data-member-form="temp_passwd">
										<?=$rand_su?>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="tac">
						<!--<input type="submit" class="button black" value="login" />-->
						<a href="?mid=member" class="button black">login</a>
						</div>
						<p>
							Please remember your temporary password and change your password after login.<br/>
						</p>
					</div>
				</div>
			</form>
		</div>
<?
} else if ($return_mode == "find_id") {
	//if (!$find_name)	error("name을 입력해 주세요.");
	//if (!$find_phone1)	error("연락처를 입력해 주세요.");
	//if (!$find_phone2)	error("연락처를 입력해 주세요.");
	//if (!$find_phone3)	error("연락처를 입력해 주세요.");
	//if (!$find_email)	error("e-mail을 입력해 주세요.");

	if($site[use_namecheck] == "Y"){
		//TODO
		$check = mysqli_fetch_array(mysqli_query($connect, "SELECT *  FROM koweb_member WHERE CI='$_SESSION[CI]' AND DI = '$_SESSION[DI]' LIMIT 1"));
		$check_where = "WHERE CI='$_SESSION[CI]' AND DI='$_SESSION[DI]' AND state = 'Y'";


		if (!$check[id])	error("There is no registered membership information.");
		if (!$check[no])	error("There is no registered membership information.");

	} else {

		//휴대폰
		//$find_phone = $find_phone1."-".$find_phone2."-".$find_phone3;
		//$check = mysqli_fetch_array(mysqli_query("SELECT * FROM koweb_member WHERE name='$find_name' AND phone='$find_phone' AND email='$find_email' AND birthday='$find_birth' AND state='Y'  LIMIT 1"));

		//e-mail
		$check = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_member WHERE name='$find_name' AND email='$find_email' LIMIT 1"));
		if (!$check[id]){
			error("There is no registered membership information.");
			exit;
		}
		if (!$check[no]) {
			error("There is no registered membership information.");
			exit;
		}

		sendMail($find_name, $find_email, $find_name."님 " . $site[title] . " Find ID result.", $site[title] . " Find ID result. <br /><br />ID : ".$check[id]."<br /><br />"."To find your password, please use our password finder.");


		$check[id] = "Your ID has been sent via a written message. <br /> If you do not have this message in your inbox, <br /> please confirm your spam box.";
	}

?>
		<div id="find_id">
			<div class="join_area">
				<h2><i>Find ID Results</i></h2>
				<div class="box find">
					<h3>Find ID Results</h3>
					<table class="bbsView">
						<caption>Find ID Results</caption>
						<colgroup>
							<col data-member-form="th" style="width:30%;"/>
							<col data-member-form="td" style="width:70%;"/>
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label for="find_result">ID</label></th>
								<td data-member-form="find_result">
									<?=$check[id]?>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="tac">
						<a href="?mid=member" class="button black"> login</a>
						<a href="?mid=member&amp;mode=find_pw" class="button black"> Find Password</a>
					</div>
					<p>
						To find your password, please use our password finder.<br/>
					</p>
				</div>
			</div>
		</div>
<?
} else {
	error("Please use the correct connection path.");
	exit;
}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/

?>
