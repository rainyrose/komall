
function windowOpen( url, w, h ) {
	var strPopupInfo = "left=10,top=10,width="+w+",height="+h+",menubar=yes,location=yes,resizable=no,scrollbars=no,status=no";
	window.open(url, "sharePopUp", strPopupInfo);
}

$(function(){
	var info_url = document.URL;
	var info_title = $("#product_title").text() +" " +$("#simple_info").text() ;


	//console.log($(location).attr('protocol')+"//"+$(location).attr('host')+$("[data-shop-view=photo] .img img").attr("src"));

	//$("head").append("<meta property=\"og:image\" id=\"og_img\" content=\""+$(location).attr('protocol')+"//"+$(location).attr('host')+$("[data-shop-view=photo] .img img").attr("src")+"\">");

	$("#facebook").click(function(){
	//	$('head').append('<meta property=\"og:image\" content="'+'http://komall.daeguweb.gethompy.com'+$("[data-shop-view=photo] .img img").attr("src")+'">');
		var url = "http://www.facebook.com/sharer.php?u=" + encodeURIComponent(info_url) + "&t=" + encodeURIComponent( info_title ) ;
		windowOpen (url, 600, 450);
	});

	$("#twitter").click(function(){
		var url = "http://twitter.com/intent/tweet?text=" + encodeURIComponent( info_title ) + "&url=" + encodeURIComponent(info_url);
		windowOpen  (url, 600, 400);
	});

	$("#naver").click(function(){
		var url = "https://share.naver.com/web/shareView.nhn?url=" + encodeURIComponent(info_url) + "&title=" + encodeURIComponent( info_title );
		windowOpen  (url, 500, 600);
	});

	if($("#kakao").length){
		//카카오톡
		Kakao.init('138d3b5c8c09b1f97349833d3fe9befb');
		Kakao.Link.createDefaultButton({
		  container: '#kakao',
		  objectType: 'feed',
		  content: {
			title: $("#product_title").text(),
			description: $("#simple_info").text(),
			imageUrl: $(location).attr('protocol')+"//"+$(location).attr('host')+"/"+$("[data-shop-view=photo] .img img").attr("src"),
			link: {
			  mobileWebUrl: $(location).attr('href'),
			  webUrl: $(location).attr('href')
			}
		  },
		  social: {

		  },
		  buttons: [
			{
			  title: '보러가기_eng',
			  link: {
				mobileWebUrl: $(location).attr('href'),
				webUrl: $(location).attr('href')
			  }
			}
		  ]
		});
	}
});
