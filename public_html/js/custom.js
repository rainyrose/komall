/* 
 * custom js Document
*/ 

$(function(){
	//ie check
	var agent = navigator.userAgent.toLowerCase();
	if (agent.indexOf("msie") > -1 || agent.indexOf("trident") > -1) {
	  	$('body').addClass('ie');
	} else if ( agent.search( "edge/" ) > -1 ){
		$('body').addClass('ie_edge');
	} else {
		//나머지브라우저 컨트롤
	}
	
	mainVisual();
	mainNav();
	noticeSlide();
	responsive();
});

$(window).load(function(){
	lnbSetControl();
});

//lnb load
function lnbSetControl(){
	if(!($('.lnb').length > 0))	return;	
	//gnb 1depth on check
	if($('#header nav .gnb > li').children('a').hasClass('on')){
		//1depth on true 2depth save
		var gnbHtml = $('#header nav .gnb > li > a.on').next('ul').html();
		//lnb add Html
		$('.lnb').append(gnbHtml);
		//nav.lnb ul add html
		$.each($('.lnb'),function(){
			$(this).children("li").wrapAll('<ul></ul>');
		})
		//lnb link #container add
		$('.lnb ul > li').each(function(){
			var lnbLink = $(this).find('a').attr('href');
			$(this).find('a').attr('href',lnbLink + '#container');
		});	

		//gnb menu on text
		var gnbTitle = $('.gnb > li > a.on').text();
		$('경로').text(gnbTitle);
		//lnb menu on text
		var lnbTitle = $('.lnb ul > li > a.on').eq(0).text();
		$('경로').text(lnbTitle);
	}else{
		//1차가 on이 없는경우 2차가 없음을 간주하고 lnb자체를 숨김처리함 불필요할때 삭제가능
		$('.lnb').hide();
	}
}

function responsive(){
	var res = '';
	var param = $('#header');
	var gnbArea = $('.gnb li');
	var gnbLink = gnbArea.children("a");
	

	//default 
	if(!($(".btn_menu").is(":hidden"))) res = "mob";
	else res = "web";  
	param.attr("class",res);
	def(param);

	$(window).resize(function(){
		if(!($(".btn_menu").is(":hidden"))) res2 = "mob";
		else res2 = "web"; 
		param.attr("class",res2);
		if(res != res2){
			res = res2;  
			def(param);
		}
	}); 

	//mobile
	$(document).on('click','.btn_close',function(){
		$('.btn_menu').removeClass('active');
		$('body, #header nav, .area_menu_all').removeClass('active');
		posY = $('body').attr('data-scroll');
		$(window).scrollTop(posY);
		return false;
	});
	
	$('.btn_menu').append('<em><i></i><i></i></em>');
	$('.btn_menu').on('click',function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			$('body, #header nav, .area_menu_all').removeClass('active');
			posY = $('body').attr('data-scroll');
			$(window).scrollTop(posY);
		}else{
			posY = window.scrollY || document.documentElement.scrollTop;
			$(this).addClass('active');
			$('body, #header nav, .area_menu_all').addClass('active');
			$('body').attr('data-scroll',posY);
		}
		return false;
	});
	
	gnbLink.hover(function() {
		$(this).addClass("active").parents('li').addClass("active"); 
		$(this).parent().hover(function() {
		}, function(){     
		   $(this).removeClass("active", function(){
			   $(this).parent().find("a").removeClass("active");
		   });    
		}); 
		
		if(!($(this).parent().find("ul").length > 0)) {
		   $(this).parent().addClass('only');
		}
	});
		
	function webGnb(){
		var gnbAllA = $('#header.web nav .gnb > li a');
		var gnbAllLi = $('#header.web nav .gnb > li li');
		
		function hide_div(){
			gnbAllLi.removeClass('active');
			$(this).parents('ul').children('li').removeClass('active');
			$(this).parents('li').toggleClass('active');
		}
		$('#header.web nav .gnb').hover(function(){
		},function(){
			$('#header.web nav .gnb li').removeClass('active');
		});
		gnbAllA.hover(hide_div);
	}
	webGnb();

	function mobGnb(){
		if(param.attr("class") == "mob"){
			$('.gnb li a, nav .gnb').unbind('mouseenter mouseleave');
			$('.area_menu_all .gnb li').each(function(){		
				var cateLink = $(this).children('a'),
				cateLi = cateLink.parent();
				if(cateLink.next('ul').length > 0){
					$('<button type="button" class="open">메뉴열림</button>').appendTo(cateLi);
				}
			});
			var gnbRoot = $('.area_menu_all'),
			gnbMenu = $('.area_menu_all .gnb li button');
			$('.area_menu_all .gnb > li ul').hide();	

			function gnbShow(){		
				var openText = $(this).text();
				$(this).parent().siblings().find("> button").removeClass("active").end().removeClass("active").children('ul').stop().slideUp('fast');
				$(this).toggleClass("active");
				$(this).text(openText == '메뉴열림' ? '메뉴닫기' : '메뉴열림');
				$(this).parent("li:first").toggleClass("active").children('ul').stop().slideToggle('fast');			
				return false;
			}
			gnbMenu.click(gnbShow);
		}
	}

	function def(param){
		if(param.attr("class") == "web"){
			$('#header nav .gnb > li > ul').removeAttr('style');
			$('#header nav .gnb > li > a').unbind('click');
			$('#header nav .area_menu_all').contents().unwrap();
			$('#header nav .btn_close').remove();
			webGnb();
			
			$('#header nav button.open').remove();
			$('.btn_menu').removeClass('active');
			$('body, #header nav, .area_menu_all').removeClass('active');

		} else if (param.attr("class") == "mob"){  			
			$('.gnb li a, nav .gnb').unbind('mouseenter mouseleave');
			$('<a href="#" class="btn_close">메뉴닫기</a>').prependTo('#header.mob nav');
			$('#header.mob nav .gnb, #header.mob nav .btn_close').wrapAll('<div class="area_menu_all"></div>');
			mobGnb();
		}
	}
}

//visual slider
function mainVisual(){
	if(!($('.area_visual').length > 0)) return false;
	
	var slider = $('.area_visual .list').bxSlider({
		auto:true,
		pager:false,
		controls:true,
	});
	function bxInit01(){
		var winSize = $(window).width(),
			sizeID;
		if(winSize < 1023){
			sizeID = true;
		}else{
			sizeID = false;
		}

		slider.reloadSlider({
			mode:'fade',
			speed:2000,
			auto:true,
			pager:false,
			controls:true,
			adaptiveHeight:sizeID,
			touchEnabled:sizeID,
		});
	}
	bxInit01();
	$(window).resize(function(){
		slider.stopAuto(true);
		bxInit01();
	});
}

function slideBest(){
	if(!($('.area_best').length > 0)) return false;
	
	var slider = $('.area_best .list').bxSlider();
	var widthMatch = matchMedia('all and (max-width: 1024px)');
	var widthHandler = function(matchList) {
		if (matchList.matches) {
			slider.reloadSlider({
				mode:'horizontal',
				auto:false,
				speed:1000,
				pager:false,
				controls:false,
				minSlides:2,
				maxSlides:3,
				slideWidth:200,
				slideMargin:0,
				touchEnabled:true,
			})
		} else {
			slider.reloadSlider({
				mode:'horizontal',
				auto:false,
				speed:1000,
				pager:false,
				minSlides:4,
				maxSlides:4,
				slideWidth:300,
				slideMargin:0,
				touchEnabled:false,
			})
		}
	};
	widthMatch.addListener(widthHandler);
	widthHandler(widthMatch);
}

function mainNav(){
	if(!($('.area_info .category').length > 0)) return false;
	var gnbSite = $('#header nav .gnb').html();
	$('.area_info .category .site').append(gnbSite);
}

function noticeSlide(){
	if(!($('#footer .notice').length > 0)) return false;
	$('#footer .notice .list').bxSlider({
		mode:'vertical',
		auto:true,
		controls:true,
		pager:false,
		pause:6000,
		nextText:'다음',
		prevText:'이전',
		
	})
	
}