<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel (optional) -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="<?php echo base_url('resources/images/default.png');?>" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p>	<?php echo $this->session->get_userdata()['fullname'];?></p>
			</div>
		</div>
		<form action="<?php echo  base_url('history/search'); ?>" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search cs, vin or engine">
				
				<span class="input-group-btn">
					<button type="submit" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
					</button>
				</span>
				
			</div>
		</form>
		<!-- Sidebar Menu -->
			<ul class="sidebar-menu">
				<li class="header">MAIN NAVIGATION</li>
				
					<?php 
				//Buyoff
				if(in_array($this->session->userdata('user_type'),array('administrator','manufacturing 1','manufacturing 2','manufacturing 3', 'vehicle releasing 1') )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'buyoff/for_buyoff' OR $this->uri->uri_string() == 'buyoff/list_' OR $this->uri->uri_string() == 'for_transfer/ivp') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-truck"></i> <span>Buyoff</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'buyoff/list_') ? 'active' : ''; ?>"><a href="<?php echo base_url('buyoff/list_'); ?>"><i class="fa fa-circle-o"></i><span>Buyoff Units</span></a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'buyoff/for_buyoff') ? 'active' : ''; ?>"><a href="<?php echo base_url('buyoff/for_buyoff'); ?>"><i class="fa fa-circle-o"></i><span>For Buyoff</span></a></li>
						<?php 
						//FOR TRANSFER
						if(in_array($this->session->userdata('user_type'),array('administrator', 'vehicle releasing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'for_transfer/ivp') ? 'active' : ''; ?>"><a href="<?php echo base_url('for_transfer/ivp'); ?>"><i class="fa fa-circle-o"></i><span>For Transfer (IVP -> IVS)</span></a></li>
						<?php 
						}
						?>
					</ul>
				</li>
				<?php 
				}
				?>
				
				<?php 
				//AVAILABLE TO RESERVE
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1', 'sales 2','vehicle releasing 1') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'forsale/summary') ? 'active' : ''; ?>"><a href="<?php echo base_url('forsale/summary'); ?>"><i class="fa fa-truck"></i><span>Available to Tag</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				//TAGGED
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1','sales 2','vehicle releasing 1') )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'tagged/list_' OR $this->uri->uri_string() == 'tagged/oc') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-truck"></i> <span>Tagged</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/list_') ? 'active' : ''; ?>"><a href="<?php echo base_url('tagged/list_'); ?>"><i class="fa fa-circle-o"></i> Tagged Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/oc') ? 'active' : ''; ?>"><a href="<?php echo base_url('tagged/oc'); ?>"><i class="fa fa-circle-o"></i> Order Confirmed</a></li>
					</ul>
				</li>
				<?php 
				}
				?>
				
				<?php 
				//On HAND UNITS
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1', 'sales 2') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'inventory/onhand' OR $this->uri->segment(2) == 'onhand_details' ) ? 'active' : ''; ?>"><a href="<?php echo base_url('inventory/onhand'); ?>"><i class="fa fa-truck"></i><span>On Hand Availability</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				//FOR INVOICE UNITS
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1', 'sales 2') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'invoice/for_invoice') ? 'active' : ''; ?>"><a href="<?php echo base_url('invoice/for_invoice'); ?>"><i class="fa fa-truck"></i><span>For Invoice</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				//INVOICED UNITS
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1', 'sales 2') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'invoice/list_') ? 'active' : ''; ?>"><a href="<?php echo base_url('invoice/list_'); ?>"><i class="fa fa-truck"></i><span>Daily Invoiced Units</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				//FOR REPAIR HISTORY //removed for future use
				if(in_array($this->session->userdata('user_type'),array('administrators') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'buyoff/view_for_repair') ? 'active' : ''; ?>"><a href="<?php echo base_url('buyoff/view_for_repair'); ?>"><i class="fa fa-ambulance"></i><span>For Repair</span></a></li>
					<li class="<?php echo ($this->uri->uri_string() == 'history/history_log') ? 'active' : ''; ?>"><a href="<?php echo base_url('history/history_log'); ?>"><i class="fa fa-book"></i><span>Repair Logs</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				//UNPULLEDOUT
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1','sales 2', 'vehicle releasing 1') )){
				?>
					<li class="<?php echo ($this->uri->uri_string() == 'unpulledout/list_') ? 'active' : ''; ?>"><a href="<?php echo base_url('unpulledout/list_'); ?>"><i class="fa fa-truck"></i><span>Unpulledout Units</span></a></li>
				<?php 
				}
				?>
				
				<?php 
				// CBU
				if(in_array($this->session->userdata('user_type'),array('administrator', 'nyk 1') )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'cbu/pullout_entry' OR $this->uri->uri_string() == 'cbu/pulledout' OR $this->uri->uri_string() == 'cbu/unpulledout') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-truck"></i> <span>NYK CBU Units</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'cbu/pullout_entry') ? 'active' : ''; ?>"><a href="<?php echo base_url('cbu/pullout_entry'); ?>"><i class="fa fa-circle-o"></i> Pullout Date Entry</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'cbu/pulledout') ? 'active' : ''; ?>"><a href="<?php echo base_url('cbu/pulledout'); ?>"><i class="fa fa-circle-o"></i> Pulledout Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'cbu/unpulledout') ? 'active' : ''; ?>"><a href="<?php echo base_url('cbu/unpulledout'); ?>"><i class="fa fa-circle-o"></i> Unpulledout Units</a></li>
					</ul>
				</li>
				<?php 
				}
				?>
				
				<?php 
				//MIS
				if(in_array($this->session->userdata('user_type'),array('administrator', 'manufacturing 1', 'manufacturing 2','manufacturing 3') )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'mis/search' OR $this->uri->uri_string() == 'mis/vins_list') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-edit"></i> <span>MIS</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'mis/search') ? 'active' : ''; ?>"><a href="<?php echo base_url('mis/search'); ?>"><i class="fa fa-circle-o"></i> Serial Numbers</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'mis/vins_list') ? 'active' : ''; ?>"><a href="<?php echo base_url('mis/vins_list'); ?>"><i class="fa fa-circle-o"></i> VINs List</a></li>
					</ul>
				</li>
				<?php 
				}
				?>
				
				<?php 
				//CSR
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1') )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'csr/list_' OR $this->uri->uri_string() == 'csr/new_' OR $this->uri->uri_string() == 'mr/display_csr_without_mr_date') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-edit"></i> <span>CSR</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'csr/list_') ? 'active' : ''; ?>"><a href="<?php echo base_url('csr/list_'); ?>"><i class="fa fa-circle-o"></i> List of CSR</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'csr/new_') ? 'active' : ''; ?>"><a href="<?php echo base_url('csr/new_'); ?>"><i class="fa fa-circle-o"></i> New Entry</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'mr/display_csr_without_mr_date') ? 'active' : ''; ?>"><a href="<?php echo base_url('mr/display_csr_without_mr_date'); ?>"><i class="fa fa-circle-o"></i> List of CSR w/out MR Date</a></li>
					</ul>
				</li>
				<?php 
				}
				?>
				
				<?php 
				//	MR ENTRY
				if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1') )){
				?>
				<li class="<?php echo ($this->uri->uri_string() == 'mr/entry') ? 'active' : ''; ?>"><a href="<?php echo base_url('mr/entry'); ?>"><i class="fa fa-edit"></i><span>MR Entry</span></a></li>
				<?php 
				} 
				?>

				<?php 
				//CRMS
				if(in_array($this->session->userdata('user_type'),array('administrator'/*, 'manufacturing 1', 'manufacturing 2'*/) )){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'crms/new_units' OR $this->uri->uri_string() == 'crms/csr' OR $this->uri->uri_string() == 'crms/so' OR $this->uri->uri_string() == 'crms/invoice' OR $this->uri->uri_string() == 'crms/pullout') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-edit"></i> <span>CRMS</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<li class="<?php echo ($this->uri->uri_string() == 'crms/new_units') ? 'active' : ''; ?>"><a href="<?php echo base_url('crms/new_units'); ?>"><i class="fa fa-truck"></i> New Vehicle</a></li>

						<li class="treeview <?php echo ($this->uri->uri_string() == 'crms/csr' OR $this->uri->uri_string() == 'crms/invoice' OR $this->uri->uri_string() == 'crms/so' OR $this->uri->uri_string() == 'crms/pullout') ? 'active' : ''; ?>">

						<a href="#">
							<i class="fa fa-edit"></i> Update 
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
							
						<ul class="treeview-menu">
								<li class="<?php echo ($this->uri->uri_string() == 'crms/csr') ? 'active' : ''; ?>">
									<a href="<?php echo base_url('crms/csr'); ?>">
										<i class="fa fa-circle-o"></i> 
										CSR
									</a>
								</li>
								<li class="<?php echo ($this->uri->uri_string() == 'crms/so') ? 'active' : ''; ?>">
									<a href="<?php echo base_url('crms/so'); ?>">
										<i class="fa fa-circle-o"></i> 
										Sales Order
									</a>
								</li>
								<li class="<?php echo ($this->uri->uri_string() == 'crms/invoice') ? 'active' : ''; ?>">
									<a href="<?php echo base_url('crms/invoice'); ?>">
										<i class="fa fa-circle-o"></i> 
										Invoice
									</a>
								</li>
								<li class="<?php echo ($this->uri->uri_string() == 'crms/pullout') ? 'active' : ''; ?>">
									<a href="<?php echo base_url('crms/pullout'); ?>">
										<i class="fa fa-circle-o"></i> 
										Pullout Date
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</li>

<!--                 <li class="treeview <?php echo ($this->uri->uri_string() == 'ows/new_units' OR $this->uri->uri_string() == 'ows/csr' OR $this->uri->uri_string() == 'ows/so' OR $this->uri->uri_string() == 'ows/invoice' OR $this->uri->uri_string() == 'ows/pullout') ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa fa-edit"></i> <span>OWS</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                     <ul class="treeview-menu">
                        <li class="<?php echo ($this->uri->uri_string() == 'ows/new_units') ? 'active' : ''; ?>"><a href="<?php echo base_url('ows/new_units'); ?>"><i class="fa fa-truck"></i> New Vehicle</a></li>

                        <li class="treeview <?php echo ($this->uri->uri_string() == 'ows/csr' OR $this->uri->uri_string() == 'ows/invoice' OR $this->uri->uri_string() == 'ows/so' OR $this->uri->uri_string() == 'ows/pullout') ? 'active' : ''; ?>">

                        <a href="#">
                            <i class="fa fa-edit"></i> Update 
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                            
                        <ul class="treeview-menu">
                                <li class="<?php echo ($this->uri->uri_string() == 'ows/csr') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('ows/csr'); ?>">
                                        <i class="fa fa-circle-o"></i> 
                                        CSR
                                    </a>
                                </li>
                                <li class="<?php echo ($this->uri->uri_string() == 'ows/so') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('ows/so'); ?>">
                                        <i class="fa fa-circle-o"></i> 
                                        Sales Order
                                    </a>
                                </li>
                                <li class="<?php echo ($this->uri->uri_string() == 'ows/invoice') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('ows/invoice'); ?>">
                                        <i class="fa fa-circle-o"></i> 
                                        Invoice
                                    </a>
                                </li>
                                <li class="<?php echo ($this->uri->uri_string() == 'ows/pullout') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('ows/pullout'); ?>">
                                        <i class="fa fa-circle-o"></i> 
                                        Pullout Date
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
				</li> -->
				<?php 
				}
				?>

				<?php 
				//	PULLOUT ENTRY
				if(in_array($this->session->userdata('user_type'),array('administrator', 'vehicle releasing 1') )){
				?>
				<li class="<?php echo ($this->uri->uri_string() == 'pullout/new_') ? 'active' : ''; ?>"><a href="<?php echo base_url('pullout/new_'); ?>"><i class="fa fa-edit"></i><span>Pullout Date Entry</span></a></li>
				<?php 
				} 
				?>
				
				<?php 
				//	REPORT
				if(in_array($this->session->userdata('user_type'),array('administrator','sales 1','sales 2','vehicle releasing 1','manufacturing 1', 'manufacturing 2'))){
				?>
				<li class="treeview <?php echo ($this->uri->uri_string() == 'forsale/report' OR $this->uri->uri_string() == 'unpulledout/report' OR $this->uri->uri_string() == 'titan/lpda_report' OR $this->uri->uri_string() == 'buyoff/buyoff_summary' OR $this->uri->uri_string() == 'titan/smda_report' OR $this->uri->uri_string() == 'motorpool/report' OR $this->uri->uri_string() == 'pullout/view_report' || $this->uri->uri_string() == 'buyoff/display_prooflist_form' OR $this->uri->uri_string() == 'report/vehicle_completion_form') ? 'active' : ''; ?>">
					<a href="#">
						<i class="fa fa-file-text-o"></i> <span>Reports</span>
						<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
						</span>
					</a>
					 <ul class="treeview-menu">
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1','sales 2', 'vehicle releasing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'reports/available_units_r/available_to_tag_excel') ? 'active' : ''; ?>"><a target='_blank' href="<?php echo base_url('reports/available_units_r/available_to_tag_excel'); ?>"><i class="fa fa-circle-o"></i>Available to Tag</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'reports/available_units_r/available_units_excel') ? 'active' : ''; ?>"><a target='_blank' href="<?php echo base_url('reports/available_units_r/available_units_excel'); ?>"><i class="fa fa-circle-o"></i>Available Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'unpulledout/report') ? 'active' : ''; ?>"><a target='_blank' href="<?php echo base_url('unpulledout/report'); ?>"><i class="fa fa-circle-o"></i>Unpulledout Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/report') ? 'active' : ''; ?>"><a href="<?php echo base_url('tagged/report'); ?>"><i class="fa fa-circle-o"></i>Tagged Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/report_oc') ? 'active' : ''; ?>"><a href="<?php echo base_url('tagged/report_oc'); ?>"><i class="fa fa-circle-o"></i>Order Confirmed</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'report/invoiced_units_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('report/invoiced_units_form'); ?>"><i class="fa fa-circle-o"></i>Invoiced Units</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'wholesale/ws_executive_report_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('wholesale/ws_executive_report_form'); ?>"><i class="fa fa-circle-o"></i>Executive Report</a></li>
						<li class="<?php echo ($this->uri->segment(2) == 'ws_summary_report') ? 'active' : ''; ?>"><a target="_blank" href="<?php echo base_url('wholesale/ws_summary_report/').date('Y'); ?>"><i class="fa fa-circle-o"></i>Wholesale Summary</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'report/inventory_management') ? 'active' : ''; ?>"><a href="<?php echo base_url('report/inventory_management'); ?>"><i class="fa fa-circle-o"></i>Inventory Management Report</a></li>
<!--
						<li class="<?php echo ($this->uri->uri_string() == 'forsale/available_units_report') ? 'active' : ''; ?>"><a target="_blank" href="<?php echo base_url('forsale/available_units_report'); ?>"><i class="fa fa-circle-o"></i>Available Units</a></li>
-->
						<li class="<?php echo ($this->uri->uri_string() == 'reports/available_units_r/available_units_summary_pdf') ? 'active' : ''; ?>"><a target="_blank" href="<?php echo base_url('reports/available_units_r/available_units_summary_pdf'); ?>"><i class="fa fa-circle-o"></i>Available Units Summary</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/oc_balance_summary_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('tagged/oc_balance_summary_form'); ?>"><i class="fa fa-circle-o"></i>OC Balance Summary</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'reports/tagged_r/OC_detailed_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('reports/tagged_r/OC_detailed_form'); ?>"><i class="fa fa-circle-o"></i>OC Detailed</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'tagged/tagged_summary_report') ? 'active' : ''; ?>"><a target="_blank" href="<?php echo base_url('tagged/tagged_summary_report'); ?>"><i class="fa fa-circle-o"></i>Tagged Units Summary</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'reports/taggged_r/tagged_units_detailed_form') ? 'active' : ''; ?>"><a target='_blank' href="<?php echo base_url('reports/tagged_r/tagged_units_detailed_form'); ?>"><i class="fa fa-circle-o"></i>Tagged Units Detailed</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'invoice/sales_daily_summary_by_dealer_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('invoice/sales_daily_summary_by_dealer_form'); ?>"><i class="fa fa-circle-o"></i>Sales Daily Range Summary By Dealer</a></li>

						<?php 
						}
						?>
						
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'vehicle releasing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'reports/for_transfer_r/for_transfer_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('reports/for_transfer_r/for_transfer_form'); ?>"><i class="fa fa-circle-o"></i>Motorpool Receiving Report</a></li>
						<?php 
						}
						?>
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'sales 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'buyoff/buyoff_summary') ? 'active' : ''; ?>"><a href="<?php echo base_url('buyoff/buyoff_summary'); ?>"><i class="fa fa-circle-o"></i>Buyoff Summary</a></li>
						<?php 
						}
						?>
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'vehicle releasing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'pullout/view_report') ? 'active' : ''; ?>"><a href="<?php echo base_url('pullout/view_report'); ?>"><i class="fa fa-circle-o"></i>Pulledout</a></li>
						
						<?php 
						}
						?>
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'manufacturing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'delivery_advisory/delivery_advisory_report') ? 'active' : ''; ?>"><a href="<?php echo base_url('delivery_advisory/delivery_advisory_report'); ?>"><i class="fa fa-circle-o"></i>Delivery Advisory</a></li>
<!-- 						<li class="<?php echo ($this->uri->uri_string() == 'titan/lpda_report') ? 'active' : ''; ?>"><a href="<?php echo base_url('titan/lpda_report'); ?>"><i class="fa fa-circle-o"></i>LPDA</a></li> -->
						<?php 
						}
						?>
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'vehicle releasing 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'motorpool/report') ? 'active' : ''; ?>"><a href="<?php echo base_url('motorpool/report'); ?>"><i class="fa fa-circle-o"></i>Motorpool Inventory</a></li>
						<?php 
						}
						?>

						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'manufacturing 1', 'manufacturing 2', 'sales 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'buyoff/display_prooflist_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('buyoff/display_prooflist_form'); ?>"><i class="fa fa-circle-o"></i>Buyoff Prooflist</a></li>
						<li class="<?php echo ($this->uri->uri_string() == 'report/vehicle_information_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('report/vehicle_information_form'); ?>"><i class="fa fa-circle-o"></i>Vehicle Information Report</a></li>
						<?php 
						}
						?>
						
						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator', 'manufacturing 1', 'sales 1') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'report/vehicle_completion_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('report/vehicle_completion_form'); ?>"><i class="fa fa-circle-o"></i>Vehicle Completion Report</a></li>
						<?php 
						}
						?>

						<?php 
						if(in_array($this->session->userdata('user_type'),array('administrator') )){
						?>
						<li class="<?php echo ($this->uri->uri_string() == 'report/vehicle_forecast_form') ? 'active' : ''; ?>"><a href="<?php echo base_url('report/vehicle_forecast_form'); ?>"><i class="fa fa-circle-o"></i>Vehicle Forecast Report</a></li>
						<?php 
						}
						?>
					</ul>
				</li>
				<?php 
				} 
				?>
				<?php if (in_array($this->session->userdata('employee_number'), array('150419', '170707', '962253', '151016'))): ?>
					<li class="<?php echo ($this->uri->uri_string() == 'sales/model_list') ? 'active' : ''; ?>"><a href="<?php echo base_url('sales/model_list'); ?>"><i class="fa fa-edit"></i><span>Vehicle Sales Model</span></a></li>
					<!--<li class="<?php echo ($this->uri->uri_string() == 'sales/pricelist') ? 'active' : ''; ?>"><a href="<?php echo base_url('sales/pricelist'); ?>"><i class="fa fa-edit"></i><span>Vehicle Sales Price</span></a></li> -->
				<?php endif; ?>
			</ul><!-- /.sidebar-menu -->
	</section>
<!-- /.sidebar -->
</aside>

