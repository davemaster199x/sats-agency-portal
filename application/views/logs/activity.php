<section class="box-typical box-typical-padding">

	<?php
	if( isset($aua_id) && $aua_id > 0 ){ ?>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="/logs/activity">Activity Logs</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="/logs/activity/<?php echo $this->uri->segment(3) ?>"><?php echo $inner_bc_txt; ?></a></li>
		  </ol>
		</nav>
	<?php
	}
	?>

	<h5 class="m-t-lg with-border"><a href="/logs/activity"><?php echo $title; ?></a></h5>

	<!-- Header -->
	<header class="box-typical-header">
		<div class="box-typical box-typical-padding">
		<?php
		$form_attr = array(
			'id' => 'jform'
		);
		echo form_open("logs/activity/{$indiv_user}",$form_attr);
		?>
            <div class="row">
                <div class="col-lg-8 columns">
			<div class="form-groupss row">


				<?php

				if( $indiv_user == '' ){ ?>
					<div class="col-lg-3 columns">
						<label for="exampleSelect" class="form-control-label">User</label>
						<div class="col-sm-12sss">
							<select  name="user" class="form-control field_g2 select2-photo">
								<option value="">---</option>
								<?php
								foreach( $pm_filter->result() as $row ){ ?>
									<option data-photo="<?php echo $this->jcclass->displayUserImage($row->photo); ?>" value="<?php echo $row->agency_user_account_id; ?>" <?php echo ( $row->agency_user_account_id == $this->input->get_post('user') )?'selected="selected"':''; ?>><?php echo "{$row->fname} {$row->lname}"; ?></option>
								<?php
								}
								?>
								<option data-photo="<?php echo $this->config->item('photo_empty'); ?>" value="0">No PM</option>
							</select>
						</div>
					</div>
				<?php
				}
				?>



				<div class="col-lg-3 columns">
					<label class="col-sm-12sss form-control-label">From</label>
					<div class="col-sm-12sss">
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo $this->input->get_post('from'); ?>">
							<input type="text" class="form-control" name="from" id="from" data-input value="<?php echo $this->input->get_post('from'); ?>" />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
					</div>
				</div>

				<div class="col-lg-3 columns">
					<label class="col-sm-12sss form-control-label">To</label>
					<div class="col-sm-12ss">
						<div class="input-group flatpickr" data-wrap="true" data-default-date="<?php echo $this->input->get_post('to'); ?>">
							<input type="text" class="form-control" name="to" id="to" data-input value="<?php echo $this->input->get_post('to'); ?>" />
							<span class="input-group-append" data-toggle>
								<span class="input-group-text">
									<i class="font-icon font-icon-calend"></i>
								</span>
							</span>
						</div>
					</div>
				</div>

				<div class="col-lg-3 columns">
					<label class="col-sm-12sss form-control-label">&nbsp;</label>
					<div class="col-sm-12ss">
						<button type="submit" class="btn btn-inline">Search</button>
					</div>
				</div>
            </div>

			</div>
            </div>
		</form>
		</div>
	</header>


	<!-- list -->
	<div class="box-typical-body">

		<div class="table-responsive">
			<table id="jtable" class="table table-hover main-table">
				<thead>
					<tr>
						<th>Date</th>
						<th>User</th>
						<th>Title</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $logs  as $index => $row ){
					?>
						<tr>
							<td><?php echo date('d/m/Y H:i',strtotime($row->created_date)); ?></td>
								<td>
								<?php
								echo $this->gherxlib->avatarv2($row->photo)."&nbsp;&nbsp;";
								
								if( $row->created_by_staff == -4 ){ // Console API

									echo "Console";		

								}else{ // default, users

									echo "{$row->fname} {$row->lname}";		
																		
								}
								?>
								</td>
							<td><?php echo $row->title_name; ?></td>
							<td>
								<?php
								echo $this->jcclass->parseDynamicLink($row);
								?>
								<input type="hidden" class="log_id" value="<?php echo $row->log_id; ?>" />
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>

		<nav aria-label="Page navigation example" style="text-align:center">

			<?php echo $pagination; ?>

		</nav>

		<div class="pagi_count"><?php echo $pagi_count; ?></div>

	</div><!--.box-typical-body-->





</section><!--.box-typical-->
<style>
.btn_save{
	margin-top: 10px;
}
.new_pass_div{
	display: none;
}
.radio {
    margin: 0;
}
.font-icon.font-icon-pencil {
    margin-right: 8px;
}
.a_link{
	border-bottom: none !important;
}
.btn_add_user{
	position: relative;
    bottom: 8px;
    margin: 0 !important;
}
</style>
<script>
jQuery(document).ready(function(){

	//init datepicker
	jQuery('.flatpickr').flatpickr({
		dateFormat: "d/m/Y"
	});

	//select2
	$(".select2-photo").not('.manual').select2({
			templateSelection: select2Photos,
			templateResult: select2Photos
	});

});
</script>