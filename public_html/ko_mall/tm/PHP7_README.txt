======================================================================

			PHP 7.x 버전 사용 가이드

======================================================================

1.lgdacom > XPayClient.php 를 "PHP7.x샘플" > XPayClient > XPayClient.php 로 변경



2.PHP 4.x, 5.x 버전에서 PHP 7.x 로 업그레이드를 하실 경우 
 "PHP7.x샘플" 폴더의 샘플소스를 사용



3. XPayClient API를 호출하는 페이지에서 변경 필요("PHP7.x샘플" 내의 샘플소스에는 변경된 소스로 모두 적용)
<기존 소스>

    $xpay->Init_TX($LGD_MID);


<변경된 소스>

    if (!$xpay->Init_TX($LGD_MID)) {
    	echo "LG유플러스에서 제공한 환경파일이 정상적으로 설치 되었는지 확인하시기 바랍니다.<br/>";
    	echo "mall.conf에는 Mert Id = Mert Key 가 반드시 등록되어 있어야 합니다.<br/><br/>";
    	echo "문의전화 LG유플러스 1544-7772<br/>";
    	exit;
    }
