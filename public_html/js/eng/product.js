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
			alert('위시리스트에 저장하였습니다_eng.')
			if(confirm('위시리스트로 이동하시겠습니까_eng?')){
				window.location.href='/eng/product/product.html?mode=wish';
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
