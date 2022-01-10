<?
if(!function_exists('session_start_samesite')) {
	function session_start_modify_cookie()
	{
		$headers = headers_list();
		krsort($headers);
		foreach ($headers as $header) {
			if (!preg_match('~^Set-Cookie: PHPSESSID=~', $header)) continue;
			$header = preg_replace('~; secure(; HttpOnly)?$~', '', $header) . '; secure; SameSite=None';
			header($header, false);
			break;
		}
	}

	function session_start_samesite($options = [])
	{
		$res = session_start($options);
		session_start_modify_cookie();
		return $res;
	}

	function session_regenerate_id_samesite($delete_old_session = false)
	{
		$res = session_regenerate_id($delete_old_session);
		session_start_modify_cookie();
		return $res;
	}
}

if(!function_exists('setcookie_samesite')) {
	function setcookie_samesite($name, $value = '', $expires = 0, $path = '', $domain = '', $secure = false, $httponly = false, $samesite = '')
	{
		if(is_array($expires)) {
			$e = $expires;
			foreach(['expires', 'path', 'domain', 'secure', 'httponly', 'samesite'] as $key) {
				if(isset($e[$key])) $$key = $e[$key];
			}
		}
		if (preg_match('~[=,; \t\r\n\x0b\x0c]~', $name)) {
			trigger_error('Cookie names cannot contain any of the following \'=,; \t\r\n\013\014\'', E_USER_WARNING);
			return false;
		}
		if (preg_match('~[,; \t\r\n\x0b\x0c]~', $path)) {
			trigger_error('Cookie paths cannot contain any of the following \',; \t\r\n\013\014\'', E_USER_WARNING);
			return false;
		}
		if (preg_match('~[,; \t\r\n\x0b\x0c]~', $domain)) {
			trigger_error('Cookie domains cannot contain any of the following \',; \t\r\n\013\014\'', E_USER_WARNING);
			return false;
		}
		$values = [];
		if (empty($value)) {
			$values[] = $name . '=delete';
			$values[] = 'expires=Thu, 01-Jan-1970 00:00:01 GMT; Max-Age=0';
		} else {
			$values[] = $name . '=' . urlencode($value);
			if ($expires != 0) {
				$values[] = 'expires=' . substr(gmdate('r', $expires), 0, -5) . 'GMT';
				$values[] = 'Max-Age=' . ($expires - time());
			}
		}
		if ($path) $values[] = 'path=' . $path;
		if ($domain) $values[] = 'domain=' . $domain;
		if ($secure) $values[] = 'secure';
		if ($httponly) $values[] = 'HttpOnly';
		if ($samesite) $values[] = 'SameSite=' . $samesite;
		header('Set-Cookie: ' . implode('; ', $values), false);
		return true;
	}
}

class XenoPostToForm
{
	public static function check() {
		return !isset($_COOKIE['PHPSESSID']) && count($_POST) && isset($_SERVER['HTTP_REFERER']) && !preg_match('~^https://'.preg_quote($_SERVER['HTTP_HOST'], '~').'/~', $_SERVER['HTTP_REFERER']);
	}

	public static function submit($posts) {
		echo '<html><head><meta charset="UTF-8"></head><body>';
		echo '<form id="f" name="f" method="post">';
		echo self::makeInputArray($posts);
		echo '</form>';
		echo '<script>';
				echo 'document.f.submit();';
				echo '</script></body></html>';
		exit;
	}

	public static function makeInputArray($posts) {
		$res = [];
		foreach($posts as $k => $v) {
			$res[] = self::makeInputArray_($k, $v);
		}
		return implode('', $res);
	}

	private static function makeInputArray_($k, $v) {
		if(is_array($v)) {
			$res = [];
			foreach($v as $i => $j) {
				$res[] = self::makeInputArray_($k.'['.htmlspecialchars($i).']', $j);
			}
			return implode('', $res);
		}
		return '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'" />';
	}
}

if(XenoPostToForm::check()) XenoPostToForm::submit($_POST); // session_start(); 하기 전에
?>
