$(document).ready(function(){
	//Replace the <textarea id="smart_content"> with a CKEditor
	//instance, using default configuration.
	if($("#smart_content").length){
		CKEDITOR.replace( 'smart_content' );
	}
	if($("#smart_content_mob").length){
		CKEDITOR.replace( 'smart_content_mob' );
	}
	if($("#smart_content2").length){
		CKEDITOR.replace( 'smart_content2' );
	}
	if($("#smart_content3").length){
		CKEDITOR.replace( 'smart_content3' );
	}
		if($("#smart_content4").length){
		CKEDITOR.replace( 'smart_content4' );
	}
		if($("#smart_content5").length){
		CKEDITOR.replace( 'smart_content5' );
	}
		if($("#smart_content6").length){
		CKEDITOR.replace( 'smart_content6' );
	}
		if($("#smart_content7").length){
		CKEDITOR.replace( 'smart_content7' );
	}
		if($("#smart_content8").length){
		CKEDITOR.replace( 'smart_content8' );
	}

	if($(".smart_content").length){
		$.each($(".smart_content"),function(i,k,v){
			CKEDITOR.replace( $(this).attr("id") );
		})
	}

	if($("#common_contents").length){
		CKEDITOR.replace( 'common_contents' );
	}

	if($("#sinfo1").length){
		CKEDITOR.replace( 'sinfo1' );
	}
		if($("#sinfo2").length){
		CKEDITOR.replace( 'sinfo2' );
	}
		if($("#sinfo3").length){
		CKEDITOR.replace( 'sinfo3' );
	}
		if($("#sinfo4").length){
		CKEDITOR.replace( 'sinfo4' );
	}
});
