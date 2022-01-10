//컨텐츠 드래그앤드랍 순서변경
$(function(){
	$("[data-drag-sort]").sortable({
		axis: 'y',
		update: function (event, ui) {
			var database = $(this).data("drag-sort");
			var data = $(this).sortable("toArray", {attribute: "data-unique-no"});
			var positions = data.join('|');
			
			$.ajax({
				type: "POST",
				url: "/ko_mall/inc/menu_sort.php",
				data : {
					sort_data: positions,
					database : database
				},
				success : function(args) {
					console.log(args);
					
				},beforeSend: function () {
					$("#div_ajax_load_image").show();
				},
				complete: function () {
					$("#div_ajax_load_image").hide();
				}
			});
		}
	});

});