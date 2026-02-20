<div id="content">
	<div class="container">
		<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="input-group">
								<input id="dashboard-search" type="text" class="form-control" placeholder="Search by name, IMEI, phone or date">
								<span class="input-group-btn">
									<button id="dashboard-search-btn" class="btn btn-primary" type="button">Search</button>
								</span>
							</div>
							<div id="dashboard-search-results" style="margin-top:10px;display:none;"></div>
						</div>
					</div>
				</div>
			<?php $this->load->view('errors/message'); ?>
			<!-- Dashboard [Start] -->
			<div id="dashboard">
				<!-- Start -->
				<div class="box col-md-3">
					<div class="cnt">
						<input class="knob" 
						data-width="110"
						data-height="110"
						data-thickness=".1"
						data-fgColor="#1E3C49"
						data-bgColor="#706d64"
						data-displayInput="false"
						value="100"
						autocomplete="off">
						<div class="status"><?php echo $total_customers ?></div>
						<h4>Customers</h4>
						<a href="<?php echo site_url('customer') ?>">View All</a>
					</div>
				</div>
				<!-- END -->			
				<!-- Start -->
				<div class="box col-md-3">
					<div class="cnt">
						<input class="knob" 
						data-width="110"
						data-height="110"
						data-thickness=".1"
						data-fgColor="#1E3C49"
						data-bgColor="#706d64"
						data-displayInput="false"
						value="<?php echo(empty($total_jobs)?'0':$pending_jobs/$total_jobs * 100); ?>"
						autocomplete="off">
						<div class="status"><?php echo $pending_jobs; ?></div>
						<h4>Pending Jobs</h4>
						<a href="<?php echo site_url('job'); ?>">View All</a>
					</div>
				</div>
				<!-- END -->
			
				<!-- Start -->
				<div class="box col-md-3">
					<div class="cnt">
						<input class="knob" 
						data-width="110"
						data-height="110"
						data-thickness=".1"
						data-fgColor="#1E3C49"
						data-bgColor="#706d64"
						data-displayInput="false"
						value="<?php echo(empty($total_jobs)?'0':$ready_jobs/$total_jobs * 100); ?>"
						autocomplete="off">
						<div class="status"><?php echo $ready_jobs; ?></div>
						<h4>Completed Jobs</h4>
						<a href="<?php echo site_url('job'); ?>">View All</a>
					</div>
				</div>
				<!-- END -->

				<!-- Start -->
				<div class="box col-md-3">
					<div class="cnt">
						<input class="knob" 
						data-width="110"
						data-height="110"
						data-thickness=".1"
						data-fgColor="#1E3C49"
						data-bgColor="#706d64"
						data-displayInput="false"
						value="<?php echo(empty($weekly_stats->income)?'0':$weekly_stats->profit/$weekly_stats->income * 100); ?>"
						autocomplete="off">
						<div class="status"><?php echo $weekly_stats->profit; ?></div>
						<h4>Weekly Profit</h4>
						<a href="<?php echo site_url('job'); ?>">View All</a>
					</div>
				</div>
				<!-- END -->				
			
				<div class="clearfix"></div><br>
			
			</div><!-- Dashboard [END] -->
		</div><!--/row-->
	</div><!--/container-->
</div>
<script>
    $(function($){
    	//Graph
        $(".knob").knob({
            draw 	: function (){},
            readOnly: true
        });
    });
</script>

<script>
// Dashboard quick search behavior
;(function($){
	function renderResults(container, data) {
		if(!data || data.length === 0) {
			container.html('<div class="alert alert-info">No results found.</div>').show();
			return;
		}
		var html = '<table class="table table-striped table-bordered"><thead><tr>' +
			'<th>ID</th><th>Name</th><th>Phone</th><th>IMEI</th><th>Receive</th><th>Delivery</th><th>Status</th><th>Action</th>' +
			'</tr></thead><tbody>';
		for(var i=0;i<data.length;i++){
			var j = data[i];
			var status = (typeof j.status !== 'undefined') ? (['Pending','Ready','Picked-up'][j.status]||j.status) : '';
			html += '<tr>' +
				'<td>'+j.id+'</td>' +
				'<td>'+ (j.name||'') +'</td>' +
				'<td>'+ (j.phone||'') +'</td>' +
				'<td>'+ (j.device_number||'') +'</td>' +
				'<td>'+ (j.receive_date||'') +'</td>' +
				'<td>'+ (j.delivery_date||'') +'</td>' +
				'<td>'+ status +'</td>' +
				'<td><a class="btn btn-xs btn-default" href="'+ '<?php echo site_url('job/edit/') ?>'+j.id+'">Edit</a> <a class="btn btn-xs btn-primary" target="_blank" href="'+ '<?php echo site_url('job/recept/') ?>'+j.id+'">Print</a></td>' +
				'</tr>';
		}
		html += '</tbody></table>';
		container.html(html).show();
	}

	$(function(){
		var $input = $('#dashboard-search');
		var $btn = $('#dashboard-search-btn');
		var $results = $('#dashboard-search-results');

		function doSearch() {
			var q = $input.val();
			if(!q || q.length < 2) {
				$results.hide();
				return;
			}
			$.ajax({
				url: '<?php echo site_url('job/search') ?>',
				type: 'POST',
				data: { q: q },
				dataType: 'json',
				success: function(resp){
					if(resp && resp.status) {
						renderResults($results, resp.data);
					} else {
						renderResults($results, []);
					}
				},
				error: function(xhr){
					console.error(xhr.responseText || xhr.statusText);
					$results.html('<div class="alert alert-danger">Search failed.</div>').show();
				}
			});
		}

		var timeout = null;
		$input.on('input', function(){
			clearTimeout(timeout);
			timeout = setTimeout(doSearch, 400);
		});
		$btn.on('click', doSearch);
	});
})(jQuery);
</script>
