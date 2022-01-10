
//<![CDATA[

$(function(){
// // 사용할 앱의 JavaScript 키를 설정해 주세요.
Kakao.init('138d3b5c8c09b1f97349833d3fe9befb');
// // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
Kakao.Link.createDefaultButton({
  container: '#kakao',
  objectType: 'feed',
  content: {
	title: $("#product_title").text(),
	description: $("#simple_info").text(),
	imageUrl: 'http://komall.daeguweb.gethompy.com'+$("[data-shop-view=photo] .img img").attr("src"),
	link: {
	  mobileWebUrl: 'http://komall.daeguweb.gethompy.com'+$(location).attr('pathname') + $(location).attr('search'),
	  webUrl: 'http://komall.daeguweb.gethompy.com'+$(location).attr('pathname') + $(location).attr('search')
	}
  },
  social: {

  },
  buttons: [
	{
	  title: '보러가기',
	  link: {
		mobileWebUrl: 'http://komall.daeguweb.gethompy.com'+$(location).attr('pathname') +$ (location).attr('search'),
		webUrl: 'http://komall.daeguweb.gethompy.com'+$(location).attr('pathname')+ $(location).attr('search')
	  }
	}
  ]
});
});
//]]>
