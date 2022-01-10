function number_format( number ){
  number = String(number);
  number=number.replace(/\,/g,"");
  nArr = String(number).split('').join(',').split('');
  for( var i=nArr.length-1, j=1; i>=0; i--, j++)  if( j%6 != 0 && j%2 == 0) nArr[i] = '';
  return nArr.join('');
}

function find_class_in_obj(className,obj){
	var childObjList = obj.children

	for(var i=0;i<childObjList.length;i++){
		var classList = childObjList[i].className.split(" ");
		if(classList.indexOf(className) >= 0){
			return childObjList[i];
			break;
		}
	}
}

//해당배열에서 | 를 기준으로 해당텍스트 검색
function check_array_filed(str,arr){
    var result = new Array();
    for(var i=0;i<arr.length;i++){
        var test = arr[i].indexOf(str);

        if(test == 0){
            var testSplit = arr[i].split("|")
            result.push(arr[i]);
        }
    }
    return result;
}

//해당배열에서 | 를 기준으로 해당텍스트 검색
function check_object_filed(str,obj){
    var result = {};
    for(var target in obj){
        var test = target.indexOf(str);

        if(test == 0){
            result[target] = obj[target];
        }
    }
    return result;
}

function wish_form_submit(productId){
    $.ajax({
		type : "POST",
		url : "/ko_mall/product/wish/ajax_set_wish.php",
		data : {
			id : productId
		},
		success : function(args) {
            var res = JSON.parse(args);
            if(res.flag == false){
                var urlList = window.location.href.split("/");
                var tepUrlList = new Array();
                for(var i=3;i<urlList.length;i++){
                    tepUrlList.push(urlList[i]);
                }
                var newUrl = "/"+tepUrlList.join("/");
                newUrl = rawurlencode(newUrl);
                window.location.href=res.url+newUrl;
                return;
            }
			alert('위시리스트에 저장하였습니다.')
			if(confirm('위시리스트로 이동하시겠습니까?')){
				window.location.href='/product/product.html?mode=wish';
			}
		}
	});
}

function rawurlencode(str) {
    str = (str + '').toString();
    return encodeURIComponent(str)
        .replace(/!/g, '%21')
        .replace(/'/g, '%27')
        .replace(/\(/g, '%28')
        .replace(/\)/g, '%29')
        .replace(/\*/g, '%2A');
}

function set_cart_no_cnt(product_id){

	$.ajax({
		type : "POST",
		url : "/ko_mall/product/cart/ajax_list_set_item_no_cnt.php",
		data : {
			"product_id":product_id
		},
		success : function(args) {
			var res = JSON.parse(args);
			if(res.flag == "STOP"){
                if(res.ment){
                    alert(res.ment);
                }
				return;
			}
			if(res.flag == "OK"){
                window.location.reload();
				// if(confirm('장바구니로 이동하시겠습니까?')){
				// 	window.location.href='?mode=cart&return_url='+encodeURIComponent($(location).attr('pathname') +$(location).attr('search'));
				// }
			} else {
				$(location).attr("href", "/member/page.html?mid=member"+ "&return_url="+encodeURIComponent($(location).attr('pathname') +$(location).attr('search')));
			}
		}
	});
}

function set_cart_no_cnt_array(){
    // alert('옵션이있는 상품은 장바구니 담기에서 제외됩니다.');
    var checkedTarget = doc.querySelectorAll("[name=check_cart]:checked");

    var len = checkedTarget.length;
    var product_id_col = new Array();
    for(var i = 0; i<len; i++){
        product_id_col.push(checkedTarget[i].getAttribute('data-wish-product-id'));
    }
    $.ajax({
        type : "POST",
        url : "/ko_mall/product/cart/ajax_check_cart.php",
        data : {
            "product_id_col":product_id_col
        },
        success : function(args) {

            var cart_flag = false
            if(args == "true"){
                if(!confirm("장바구니에 동일한 상품이 있습니다. 장바구니에 추가하시겠습니까?")){
                    cart_flag = true
                }
            }
            $.ajax({
                type : "POST",
                url : "/ko_mall/product/cart/ajax_list_set_item_no_cnt_array.php",
                data : {
                    "product_id_col":product_id_col,
                    "cart_flag":cart_flag
                },
                success : function(args) {
                    var res = JSON.parse(args);
                    console.log(res);
                    if(res.ment){
                        alert(res.ment);
                    }
                    if(res.flag == "STOP"){
                        return;
                    }
                    if(res.flag == "OK"){
                        if(confirm('장바구니로 이동하시겠습니까?')){
                            window.location.href='?mode=cart&return_url='+encodeURIComponent($(location).attr('pathname') +$(location).attr('search'));
                        }else{
                            location.reload();
                        }
                    } else {
                        $(location).attr("href", "/member/page.html?mid=member"+ "&return_url="+encodeURIComponent($(location).attr('pathname') +$(location).attr('search')));
                    }
                }
            });
        }
    });
}


(function () {
  var forEach = [].forEach,
      regex = /^data-(.+)/,
      dashChar = /\-([a-z])/ig,
      el = document.createElement('div'),
      mutationSupported = false,
      match
  ;

  function detectMutation() {
    mutationSupported = true;
    this.removeEventListener('DOMAttrModified', detectMutation, false);
  }

  function toCamelCase(s) {
    return s.replace(dashChar, function (m,l) { return l.toUpperCase(); });
  }

  function updateDataset() {
    var dataset = {};
    forEach.call(this.attributes, function(attr) {
      if (match = attr.name.match(regex))
        dataset[toCamelCase(match[1])] = attr.value;
    });
    return dataset;
  }

  // only add support if the browser doesn't support data-* natively
  if (el.dataset != undefined) return;

  el.addEventListener('DOMAttrModified', detectMutation, false);
  el.setAttribute('foo', 'bar');

  function defineElementGetter (obj, prop, getter) {
    if (Object.defineProperty) {
        Object.defineProperty(obj, prop,{
            get : getter
        });
    } else {
        obj.__defineGetter__(prop, getter);
    }
  }

  defineElementGetter(Element.prototype, 'dataset', mutationSupported
    ? function () {
      if (!this._datasetCache) {
        this._datasetCache = updateDataset.call(this);
      }
      return this._datasetCache;
    }
    : updateDataset
  );

  document.addEventListener('DOMAttrModified', function (event) {
    delete event.target._datasetCache;
  }, false);
})();
