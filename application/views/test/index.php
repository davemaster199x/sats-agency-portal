<?php
if( isset($_POST['crop_image_base64']) ){
	
	
	$data = $_POST['crop_image_base64'];

	list($type, $data) = explode(';', $data);
	list(, $data)      = explode(',', $data);
	$data = base64_decode($data);
	
	$image_name = 'crop_image'.rand().date('Ymdhi');
	$temp_crop_file = $_SERVER['DOCUMENT_ROOT']."/uploads/temp/{$image_name}.png";
	$upload_path = $_SERVER['DOCUMENT_ROOT']."/uploads/user_accounts/photo/{$image_name}.png";

	file_put_contents($temp_crop_file, $data);
	rename($temp_crop_file,$upload_path);
	
	
}
?>
<div class="container-fluid">

<section class="box-typical">
<form action="/test/index" id="jform" enctype="multipart/form-data" method="post" accept-charset="utf-8">

<input type="file" class="form-control-file" name="upload" id="upload" />
<button type="button" class="btn_crop">Crop</button>
<button type="button" class="btn_upload">Upload</button>
<input type="hidden" id="crop_image_base64" name="crop_image_base64" />


<div class="upload-demo-wrap">
	<div id="upload-demo"></div>
</div>

<img id="crop_result" src='' />

</form>
</section>
</div>

<style>
.upload-demo .upload-demo-wrap,
.upload-demo .btn_crop,
.upload-demo.ready .upload-msg {
    display: none;
}
.upload-demo.ready .upload-demo-wrap {
    display: block;
}
.upload-demo.ready .btn_crop {
    display: inline-block;    
}
.upload-demo-wrap {
    width: 300px;
    height: 300px;
    margin: 0 auto;
}

.upload-msg {
    text-align: center;
    padding: 50px;
    font-size: 22px;
    color: #aaa;
    width: 260px;
    margin: 50px auto;
    border: 1px solid #aaa;
}
</style>
<script>
var $uploadCrop;

// read file from input
function readFile(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('.upload-demo').addClass('ready');
			$uploadCrop.croppie('bind', {
				url: e.target.result
			}).then(function(){
				console.log('jQuery bind complete');
			});
			
		}
		
		reader.readAsDataURL(input.files[0]);
	}
	else {
		swal("Sorry - you're browser doesn't support the FileReader API");
	}
}

// croppie settings
$uploadCrop = $('#upload-demo').croppie({
	viewport: {
		width: 200,
		height: 200,
		type: 'circle'
	},
	enableExif: true
});

// read file from input
$('#upload').on('change', function () { readFile(this); });

// crop
$('.btn_crop').on('click', function () {
	
	$uploadCrop.croppie('result', {
		type: 'base64'
	}).then(function (resp) {
		
		jQuery("#crop_result").attr("src",resp);
		
	});
	
});

// crop
$('.btn_upload').on('click', function () {
	
	$uploadCrop.croppie('result', {
		type: 'base64'
	}).then(function (resp) {
		
		jQuery("#crop_image_base64").val(resp);
		jQuery("#jform").submit();
		
	});
	
});
</script>


