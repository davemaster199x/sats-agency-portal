<div class="box-typical box-typical-padding form-profile vpd_box">

	<h5 class="m-t-lg with-border"><a href="/resources"><?php echo $title; ?></a></h5>

    <!-- PROPERTY MANAGERS -->
	<div class="row">
		<div class="col-lg-6">

			<?php foreach($resource_headers as $rh) { ?>

				<div class="main_resource_div">
					<header class="box-typical-header">
						<div class="tbl-row">
							<div class="tbl-cell tbl-cell-title">
								<h3>
									<span class="glyphicon glyphicon-map-marker"></span>
									<?php echo $rh->name; ?>
								</h3>
							</div>
						</div>
					</header>
					<section class="box-typical-123">
						<div class="box-typical-body">
							<div class="table-responsive">
								<table class="table table-hover main-table">
									<thead>
										<tr>
											<th class="header_icon"></th>
											<th class="header_name">Name</th>
											<th class="header_date">Date</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// listing
										foreach($rh->resource_data as $res){

											if ( $res->type == 1 ) { // file

												if( strpos($res->filename,".doc") != false ){ // wordoc
													$file_icon = 'file-word-o';
												}else if( strpos($res->filename,".pdf") != false ){ // pdf
													$file_icon = 'file-pdf-o';
												}else{ // other
													$file_icon = 'file-o';
												}

												if( strpos($res->path, 'agent_documents') !== false ){
													$url = "{$this->config->item('crmci_link')}{$res->path}{$res->filename}";
												}else{
													$url = "{$this->config->item('crm_link')}/resources/{$country_iso}/{$res->filename}";
												}
											

											} else if( $res->type == 2 ) { // link

												// youtube
												if( strpos($res->url,"youtu") != false ){
													$file_icon = 'youtube-play';
												}else{ // other link
													$file_icon = 'external-link';
												}

												$url = $res->url;

											}

										?>
										<tr>
											<td>
												<a href="javascript:void(0);" target="blank">
													<i class="fa fa-<?php echo $file_icon; ?>"></i>
												</a>
											</td>
											<td>
												<a href="<?php echo $url; ?>" target="_blank">
													<?php echo $res->title; ?>
												</a>
											</td>
											<td>
												<?php echo date('d/m/Y',strtotime($res->date)); ?>
											</td>
										</tr>
										<?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</section>
				</div>

			<?php } ?>


		</div>
	</div>

</div>

<style>
.gd-doc {
    width: 200px;
	float: left;
	margin: 0;
}
.gd-doc i.fa{
	font-size: 30px;
}
.res_header{
	clear: both;
	margin-top: 50px;
}
.header_icon{
	width: 1%;
}
.header_name{
	width: 62%;
}
.header_date{
	width: 3%;
}
.main_resource_div {
    margin-top: 20px;
}
</style>
