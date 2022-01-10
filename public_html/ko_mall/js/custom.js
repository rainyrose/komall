/* 
 * custom js Document
*/ 

//scroll bar
$(function(){ 
	jQuery('.scrollbar-dynamic').scrollbar();
	jQuery('.scrollbar-inner').scrollbar();
});

$(function(){
	$('#header ul li a').wrapInner('<span></span>');
	$('#header h2 a').on('click',function(){
		$(this).toggleClass('active');
		$(this).parent().next('ul').slideToggle('fast');
	});
});

$(function(){
	$('.area_util .btn_menu').on('click',function(){
		var listText = $(this).text();
		$(this).toggleClass('active');
		$('.area_util .btn_menu').text(listText == 'Menu Open' ? 'Menu Close' : 'Menu Open');
		$('#header').toggleClass('active');
		$('#contanier').toggleClass('active');
		return false;
	});
});

function funLoad(){	
	$('#header .scrollbar-dynamic').css('height',$(window).height());
	$('#contanier .scrollbar-dynamic').css('height',$(window).height() - 59);
}
$(window).resize(function(){ 
	funLoad();
});
$(function(){
	funLoad();
});

//table
$(function(){
	$('.table.hover td').on('mouseover',function(){
		$('.table.hover tr').removeClass('active');
		$(this).parent('tr').addClass('active');
	});
	
	$('.table.hover').on('mouseleave',function(){
		$('.table.hover tr').removeClass('active');
	});
});

//layout pop
$(function(){
	$('.popBox').append('<a href="#" class="btn_close">팝업닫기</a>');
	$('.btn_close').on('click',function(){
		 $('.popBox').parent('div').removeClass('active').fadeOut();
		 $('body').removeClass('active');
		 return false;
	});
	$(document).mouseup(function(e){
		var container = $('.popBox').parent('div'); 
		if(container.has(e.target).length == 0){
			container.removeClass('active').fadeOut();
			$('body').removeClass('active');
		}
	});
});
jQuery.fn.layerCenter = function(){
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2)) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2)) + "px");
    return this;
}

/* setting.js move
function showPopup(el){
	var $el = $(el);
	$el.fadeIn();
	$el.children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$el.addClass('active');
	}, 100);
	return false;
}
*/

//cms layout
$(window).load(function(){
	if($('#contanier').hasClass('cmsapp')){
		$('#content').wrapInner('<div class="box pd"></div>');
	}
});

//accordion
$(function(){
	$('.accordion > ul').each(function(){
		var accordiLi = $(this).children('li');
		var accordiA = accordiLi.children('a');
		var accordiUl = accordiLi.children('ul');
		accordiA.click(function(){
			$(this).toggleClass('active').next('ul').slideToggle();
			return false;
		});
		if(accordiUl.length > 0){
			$(this).parent('')
		}
	});
	
	$('.product_filter > a').on('click',function(){
		$('.box_filter').slideToggle();
	});
});

//hover pop
$(function(){
	$('.area_view_pop .view').hover(function(){
		$(this).parent().find('.info').addClass('active');
	});
	
	$('.area_view_pop').on('mouseleave',function(){
		$(this).find('.info').removeClass('active');
	});
});