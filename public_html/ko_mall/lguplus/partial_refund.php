<?php
    /*
     * [결제 부분취소 요청 페이지]
     *
     * 토스페이먼츠으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
     * (승인시 토스페이먼츠으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
     */
    $CST_PLATFORM         		= "service";						//토스페이먼츠 결제 서비스 선택(test:테스트, service:서비스)
    $CST_MID              		= $_POST["CST_MID"];							//상점아이디(토스페이먼츠으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                  				//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID              		= (("test" == $CST_PLATFORM)?"t":"").$CST_MID; 	//상점아이디(자동생성)
    $LGD_TID              		= $_POST["LGD_TID"];							//토스페이먼츠으로 부터 내려받은 거래번호(LGD_TID)
    $LGD_CANCELAMOUNT     		= $_POST["LGD_CANCELAMOUNT"];					//부분취소 금액
    // $LGD_REMAINAMOUNT     		= $_POST["LGD_REMAINAMOUNT"];					//취소전 남은금액

    // $LGD_CANCELTAXFREEAMOUNT    = $_POST["LGD_CANCELTAXFREEAMOUNT"];			//면세대상 부분취소 금액 (과세/면세 혼용상점만 적용)
    $LGD_CANCELREASON     		= "쇼핑몰 부분취소";					//취소사유
    // $LGD_RFACCOUNTNUM           = $_POST["LGD_RFACCOUNTNUM"];					//환불계좌 번호(가상계좌 환불인경우만 필수)
    // $LGD_RFBANKCODE             = $_POST["LGD_RFBANKCODE"];						//환불계좌 은행코드(가상계좌 환불인경우만 필수)
    // $LGD_RFCUSTOMERNAME         = $_POST["LGD_RFCUSTOMERNAME"];					//환불계좌 예금주(가상계좌 환불인경우만 필수)
    // $LGD_RFPHONE                = $_POST["LGD_RFPHONE"];						//요청자 연락처(가상계좌 환불인경우만 필수)

    $configPath 				= $_SERVER['DOCUMENT_ROOT']."/../lguplus"; 						 		//토스페이먼츠에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

    require_once($_SERVER['DOCUMENT_ROOT']."/ko_mall/lguplus/lgdacom/XPayClient.php");

	// (1) XpayClient의 사용을 위한 xpay 객체 생성
	// (2) Init: XPayClient 초기화(환경설정 파일 로드)
	// configPath: 설정파일
	// CST_PLATFORM: - test, service 값에 따라 lgdacom.conf의 test_url(test) 또는 url(srvice) 사용
	//				- test, service 값에 따라 테스트용 또는 서비스용 아이디 생성
    $xpay = new XPayClient($configPath, $CST_PLATFORM);

	// (3) Init_TX: 메모리에 mall.conf, lgdacom.conf 할당 및 트랜잭션의 고유한 키 TXID 생성
    $xpay->Init_TX($LGD_MID);

    $xpay->Set("LGD_TXNAME", "PartialCancel");
    $xpay->Set("LGD_TID", $LGD_TID);
    $xpay->Set("LGD_CANCELAMOUNT", $LGD_CANCELAMOUNT);
    $xpay->Set("LGD_REMAINAMOUNT", $LGD_REMAINAMOUNT);
//    $xpay->Set("LGD_CANCELTAXFREEAMOUNT", $LGD_CANCELTAXFREEAMOUNT);
    $xpay->Set("LGD_CANCELREASON", $LGD_CANCELREASON);
    // $xpay->Set("LGD_RFACCOUNTNUM", $LGD_RFACCOUNTNUM);
    // $xpay->Set("LGD_RFBANKCODE", $LGD_RFBANKCODE);
    // $xpay->Set("LGD_RFCUSTOMERNAME", $LGD_RFCUSTOMERNAME);
    // $xpay->Set("LGD_RFPHONE", $LGD_RFPHONE);
	$xpay->Set("LGD_REQREMAIN", "1");

    /*
     * 1. 결제 부분취소 요청 결과처리
     *
     */
	// (4) TX: lgdacom.conf에 설정된 URL로 소켓 통신하여 결제부분취소요청, 결과값으로 true, false 리턴
    if ($xpay->TX()) {
        $partial_refund_flag = true;
		// (5) 결제 부분취소 요청 결과 처리
        //1)결제 부분취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
        if($xpay->Response_Code() == "0000"){
            echo "결제 부분취소 요청이 완료되었습니다.  <br>";
        }else{
            $partial_refund_flag = false;
            echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
            echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
            $keys = $xpay->Response_Names();
            foreach($keys as $name) {
                echo $name . " = " . $xpay->Response($name, 0) . "<br>";
        	}
        }
        //
        //     exit;
        // echo "<p>";



    }else {
        $partial_refund_flag = false;
        //2)API 요청 실패 화면처리
        echo "결제 부분취소 요청이 실패하였습니다.  <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
    }
?>
