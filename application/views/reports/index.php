<section class="box-typical box-typical-padding">

	<h5 class="m-t-lg with-border"><a href="/reports"><?php echo $title; ?></a></h5>


	<!-- list -->
	<div class="box-typical-body">

		<div class="table-responsive">
			<table class="table table-hover main-table" id="datatable">
				<thead>
					<tr>
						<th>Report Name</th>
						<th>Description</th>
						<th>View</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Completed Jobs</td>
						<td>All jobs that have been completed</td>
						<td>
							<a href="/reports/completed_jobs">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>Active Services</td>
						<td>All properties that are currently serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?></td>
						<td>
							<a href="/reports/active_services">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>New Tenancy</td>
						<td>All New Tenancy jobs that are yet to be completed</td>
						<td>
						<a href="/reports/new_tenancy">
							<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>Not Serviced</td>
						<td>All properties that are currently NOT serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?></td>
						<td>
						<a href="/reports/not_serviced">
							<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>Not Compliant</td>
						<td>All properties that are NOT compliant by <?=$this->config->item('COMPANY_NAME_SHORT')?></td>
						<td>
						<a href="/reports/not_compliant">
							<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<?php
					if($agency_state == 'QLD'){
						?>

					<tr>
						<td>QLD Upgrade</td>
						<td>All properties that DO NOT meet the new QLD Legislation</td>
						<td>
							<a href="/reports/qld_upgrade">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>QLD Upgrade Quotes</td>
						<td>All properties with a valid upgrade quote</td>
						<td>
							<a href="/reports/qld_upgrade_quotes">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>QLD Approved Quotes</td>
						<td>All Properties that you have approved to be upgraded</td>
						<td>
							<a href="/reports/approved_qld_upgrade_quotes">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<tr>
						<td>Upgraded Properties</td>
						<td>Upgraded Properties</td>
						<td>
							<a href="/reports/upgraded_properties">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<?php
					}
					?>

					<?php
					// only show for agency with upfront billing enabled
					if( $numberOfAgencies > 0 ){ ?>

						<tr>
							<td>Subscription Dates</td>
							<td>All Properties under subscription and their month of next invoice</td>
							<td>
								<a href="/reports/subscription_dates">
									<button type="button" class="btn btn-inline btn_view">View</button>
								</a>
							</td>
						</tr>

					<?php
					}
					?>

					<tr>
						<td>Key Pick Up</td>
						<td>All jobs booked with key access</td>
						<td>
							<a href="/reports/key_pick_up">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>

					<?php if($check_agency_accounts_reports_preference) { ?>
					<tr>
						<td>Unpaid Invoices</td>
						<td>All jobs with unpaid invoices</td>
						<td>
							<a href="/reports/unpaid_invoices">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>

					<tr>
						<td>Overdue Invoices</td>
						<td>All jobs with overdue invoices</td>
						<td>
							<a href="/reports/overdue_invoices">
								<button type="button" class="btn btn-inline btn_view">View</button>
							</a>
						</td>
					</tr>
					<?php } ?>

					<?php
					// only show on AU and hume housing
					$agency_id = 1598; //  hume housing
					//$agency_id = 1448; //  adams
					if( $this->session->country_id == 1 && $this->session->agency_id == $agency_id ){ ?>

						<tr>
							<td>Expiring Alarms</td>
							<td>Use this page to forecast alarms expiring in all properties in a given year</td>
							<td>
								<a href="/reports/expiring_alarms_hume">
									<button type="button" class="btn btn-inline btn_view">View</button>
								</a>
							</td>
						</tr>

						
						<tr>
							<td>Hume Job Logs</td>
							<td>This page shows recent logs for outstanding jobs</td>
							<td>
								<a href="/reports/hume_job_logs">
									<button type="button" class="btn btn-inline btn_view">View</button>
								</a>
							</td>
						</tr>
						

					<?php
					}
					?>

					<?php					
					if($agency_state == 'NSW'){
					?>
						<tr>
							<td>STRA Non-Compliant</td>
							<td>All properties that DO NOT meet the new STRA requirements</td>
							<td>
								<a href="/reports/stra/?compliant=0">
									<button type="button" class="btn btn-inline btn_view">View</button>
								</a>
							</td>
						</tr>
						<tr>
							<td>STRA Compliant</td>
							<td>All properties that are smoke alarm compliant for STRA requirements</td>
							<td>
								<a href="/reports/stra/?compliant=1">
									<button type="button" class="btn btn-inline btn_view">View</button>
								</a>
							</td>
						</tr>
					<?php
					}
					?>

				</tbody>
			</table>
		</div>



	</div><!--.box-typical-body-->





</section><!--.box-typical-->
