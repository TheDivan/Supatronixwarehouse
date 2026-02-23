<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="container">
  <h2>Collections Report</h2>
  <ul class="nav nav-tabs" id="collections-tabs">
    <li class="active"><a href="#weekly" data-toggle="tab">Weekly Collections</a></li>
    <li><a href="#monthly" data-toggle="tab">Monthly Collections</a></li>
  </ul>

  <div class="tab-content" style="margin-top:15px;">
    <div class="tab-pane active" id="weekly">
      <div class="row">
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>Total Devices Collected</h4>
              <h2><?php echo isset($weekly_summary['total_devices']) ? $weekly_summary['total_devices'] : 0; ?></h2>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>Total Revenue for the Week</h4>
              <h2>N$ <?php echo number_format(isset($weekly_summary['total_revenue']) ? $weekly_summary['total_revenue'] : 0, 2); ?></h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row" style="margin-bottom:10px;">
        <div class="col-md-12">
          <button id="print-weekly" class="btn btn-default">Print</button>
          <a class="btn btn-primary" href="<?php echo site_url('collections/export_csv/weekly'); ?>">Export to CSV</a>
        </div>
      </div>

      <div id="weekly-content">
        <table class="table table-bordered table-striped" id="weekly-table">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer Name</th>
              <th>Device Type</th>
              <th>Device Model</th>
              <th>Fault Description</th>
              <th>Date Booked</th>
              <th>Date Ready</th>
              <th>Date Collected</th>
              <th>Amount Charged</th>
              <th>Payment Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($weekly as $r): ?>
            <tr>
              <td><?php echo $r['id']; ?></td>
              <td><?php echo $r['customer_name']; ?></td>
              <td><?php echo $r['device_type']; ?></td>
              <td><?php echo $r['device_model']; ?></td>
              <td><?php echo $r['fault_description']; ?></td>
              <td><?php echo $r['date_booked']; ?></td>
              <td><?php echo $r['date_ready']; ?></td>
              <td><?php echo $r['collected_date']; ?></td>
              <td>N$ <?php echo number_format($r['amount_charged'],2); ?></td>
              <td><?php echo $r['payment_status']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="definitions" style="margin-top:20px;">
          <h4>Definitions</h4>
          <p><strong>Booking ID:</strong> Unique reference number assigned to each repair job</p>
          <p><strong>Date Ready:</strong> The date the technician marked the device as ready for collection</p>
          <p><strong>Date Collected:</strong> The date the customer physically collected the device</p>
          <p><strong>Amount Charged:</strong> Final invoice total charged to the customer for parts and labour</p>
          <p><strong>Payment Status:</strong> Indicates whether payment was received at time of collection</p>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="monthly">
      <div class="row">
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>Total Devices Collected</h4>
              <h2><?php echo isset($monthly_summary['total_devices']) ? $monthly_summary['total_devices'] : 0; ?></h2>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-body">
              <h4>Total Revenue for the Month</h4>
              <h2>N$ <?php echo number_format(isset($monthly_summary['total_revenue']) ? $monthly_summary['total_revenue'] : 0, 2); ?></h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row" style="margin-bottom:10px;">
        <div class="col-md-12">
          <button id="print-monthly" class="btn btn-default">Print</button>
          <a class="btn btn-primary" href="<?php echo site_url('collections/export_csv/monthly'); ?>">Export to CSV</a>
        </div>
      </div>

      <div id="monthly-content">
        <table class="table table-bordered table-striped" id="monthly-table">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Customer Name</th>
              <th>Device Type</th>
              <th>Device Model</th>
              <th>Fault Description</th>
              <th>Date Booked</th>
              <th>Date Ready</th>
              <th>Date Collected</th>
              <th>Amount Charged</th>
              <th>Payment Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($monthly as $r): ?>
            <tr>
              <td><?php echo $r['id']; ?></td>
              <td><?php echo $r['customer_name']; ?></td>
              <td><?php echo $r['device_type']; ?></td>
              <td><?php echo $r['device_model']; ?></td>
              <td><?php echo $r['fault_description']; ?></td>
              <td><?php echo $r['date_booked']; ?></td>
              <td><?php echo $r['date_ready']; ?></td>
              <td><?php echo $r['collected_date']; ?></td>
              <td>N$ <?php echo number_format($r['amount_charged'],2); ?></td>
              <td><?php echo $r['payment_status']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="definitions" style="margin-top:20px;">
          <h4>Definitions</h4>
          <p><strong>Booking ID:</strong> Unique reference number assigned to each repair job</p>
          <p><strong>Date Ready:</strong> The date the technician marked the device as ready for collection</p>
          <p><strong>Date Collected:</strong> The date the customer physically collected the device</p>
          <p><strong>Amount Charged:</strong> Final invoice total charged to the customer for parts and labour</p>
          <p><strong>Payment Status:</strong> Indicates whether payment was received at time of collection</p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@media print {
  nav#mainMenu, .btn, .nav-tabs { display: none !important; }
  .definitions { display: block; }
}
</style>

<script>
;(function(){
  document.getElementById('print-weekly').addEventListener('click', function(){
    window.print();
  });
  document.getElementById('print-monthly').addEventListener('click', function(){
    window.print();
  });
})();
</script>
