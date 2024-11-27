// button toggle script
function buttonToogleScript(btn_id_or_class,orig_btn_txt,show_div,parent_elem='tr:first',cancel_btn_txt='Cancel'){
	
	
	jQuery(btn_id_or_class).click(function(){
		
		var obj = jQuery(this);
		var this_row = obj.parents(parent_elem);
		var btn_txt = obj.html();		
		
		if( btn_txt == orig_btn_txt ){
			obj.html(cancel_btn_txt);
			this_row.find(show_div).show();
		}else{
			obj.html(orig_btn_txt);
			this_row.find(show_div).hide();
		}
	
		
	});
	
}

// copy to clipboard
function copy_to_clipboard(text) {

	var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();

}