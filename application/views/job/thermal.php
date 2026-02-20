<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Job Card</title>
<link href="<?php echo base_url(); ?>assets/styles/thermal.css" rel="stylesheet" type="text/css" />
</head>
<body onload="setTimeout(function(){ window.print(); },200);">
<?php
ob_start();
?>
<div class="copy">
  <div id="main">
    <div id="logo"> <img src="<?php echo base_url(); ?>assets/images/supalogo.png" style="width:80%" /></div>
    <div class="contentbox">
    <p>Job Number: <strong style="float:right"><?php echo $job['id'] ?></strong></p>
    <p>Tel: <strong style="float:right">+264 64 20 3318</strong></p>
    <p>E-mail: <strong style="float:right">repairs@supatronix.com</strong></p>
        <hr>
    <h4 style="margin-bottom:5px; margin-top:10px;">Job Information</h4>
    <p>Receive Date: <strong style="float:right"><?php echo date('d-M-Y', strtotime($job['receive_date'])) ?></strong></p>
    <p>Delivery Date: <strong style="float:right"><?php echo date('d-M-Y', strtotime($job['delivery_date'])) ?></strong></p>
    <p>Technician: <strong style="float:right"><?php echo $job['technician'] ?></strong></p>
    <p>Location: <strong style="float:right"><?php echo $job['office'] ?></strong></p>
    <hr>
    <h4 style="margin-bottom:5px; margin-top:10px;">Customer Information</h4>
    <p>Name: <strong style="float:right"><?php echo $job['name'] ?></strong></p>
    <p>Phone: <strong style="float:right"><?php echo $job['phone'] ?></strong></p>
    <p>Email: <strong style="float:right"><?php echo $job['email'] ?></strong></p>
    <hr>
    <h3 style="margin-bottom:8px; margin-top:10px;">Device Information</h3>
<?php $total = 0.00; ?>      
<?php foreach ($job['items'] as $k => $device): ?>   
  <?php $devinfo = $device['brand'].' - '.$device['model'].' ('.$device['color'].')'; ?>
  <p><strong><span class="truncate"><?php echo strlen($devinfo)>40?substr($devinfo,0,37).'...':$devinfo ?></span></strong></p>
  <p><strong>IMEI/ ESN/ SN:</strong> <span class="truncate"><?php echo $device['device_number'] ?></span><strong style="float:right">N$<?php echo number_format($device['amount'], 2) ?></strong></p>
  <p><strong>Fault(s):</strong>
  <?php $faults = ''; $faults .= $device['battery'] ? '' : 'Battery, '; $faults .= $device['charging'] ? '' : 'Charging, '; $faults .= $device['network'] ? '' : 'Network, '; $faults .= $device['display'] ? '' : 'Display, '; $faults .= $device['camera'] ? '' : 'Camera, '; $faults .= $device['power_on'] ? '' : 'Power On'; $faults = trim($faults, ', '); ?>
  <span class="truncate"><?php echo strlen($faults)>60?substr($faults,0,57).'...':$faults ?></span>
  </p>
    <hr style="border:0; border-top:1px solid #999">
<?php $total += floatval($device['amount']); ?>    
<?php endforeach ?>
    <p><strong>TOTAL</strong><strong style="float:right">N$<?php echo number_format($total, 2) ?></strong></p>
    </div>
    <h4 style="margin:0 0 6px 0; text-align:left;">Terms And Conditions</h4>
    <ol style="line-height:1.35; text-align:left; margin:6px 0 0 18px; font-size:12px;">
      <li>A non-refundable deposit fee of N$100 will be charged upon handing in a phone.</li>
      <li>Repairs carry a three month warranty. Liquid damage or no network shall be warranted on assessment.</li>
      <li>Phones not collected within 90 days will be sold to defray expenses.</li>
      <li>For security reasons, the phone cannot be collected without presenting this job card.</li>
      <li>Supatronix is not responsible for any data loss during the repair process. Customers are advised to back up their data before submitting their devices for repair.</li>
      <li>By handing in the above mentioned phone for repairs, I acknowledge and accept these terms and conditions.</li>
    </ol>
    <p style="margin-top:8px; font-weight:bold; text-align:left;">PLEASE RETAIN THIS RECEIPT FOR YOUR RECORDS, WITHOUT THIS WE WILL NOT SERVE YOU.</p>

    <div style="display:flex; justify-content:space-between; margin-top:12px;">
      <div id="signcompny"></div>
      <div id="signcus"></div>
    </div>

    </div>
  </div>
</div>
<?php
$copy = ob_get_clean();
// Print two labeled copies side-by-side with dashed divider
$left = '<div class="copy"><div class="copy-label">Customer Copy</div>' . $copy . '</div>';
$right = '<div class="copy"><div class="copy-label">Shop Copy</div>' . $copy . '</div>';
echo '<div class="twoup">' . $left . '<div class="divider"></div>' . $right . '</div>';
?>
</body>
</html>