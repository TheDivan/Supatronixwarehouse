<!DOCTYPE html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>
<head>
<meta charset="utf-8">
<title>Job Card</title>
<link href="<?php echo base_url(); ?>assets/styles/print.css" rel="stylesheet" type="text/css" />
</head>
<body onload="window.print();">
<?php ob_start(); ?>
<header class="clearfix">
  <div id="logo"><img src="<?php echo base_url(); ?>assets/images/supalogo.png" />
    <div id="center_title">Check-In Receipt</div>
  </div>
  <div id="project">
    <div>Tel: <strong>+264 64 203318</strong></div>
    <div>E-mail: <strong>repairs@supatronix.com</strong></div>
  </div>
</header>
<main>
  <div id="cinfo_table_rgt">
    <table width="100%" border="0">
      <tr>
        <th id="th_center" colspan="2" scope="col">Job Information </th>
      </tr>
      <tr>
        <td width="45%">Receive Date </td>
        <td width="55%">: <?php echo date('d-M-Y', strtotime($job['receive_date'])) ?></td>
      </tr>
      <tr>
        <td width="45%">Delivery Date </td>
        <td>: <?php echo date('d-M-Y', strtotime($job['delivery_date'])) ?></td>
      </tr>
      <tr>
        <td width="45%">Technician </td>
        <td>: <?php echo $job['technician'] ?></td>
      </tr>
      <tr>
        <td width="42%">Location</td>
        <td>: <?php echo $job['office'] ?></td>
      </tr>
    </table>
  </div>
  <div id="cinfo_table">
    <table width="100%" border="0" cellspacing="0">
      <tr>
        <th id="th_center" colspan="2" scope="col">Customer Information </th>
      </tr>
      <tr>
        <td width="5%">NAME </td>
        <td width="95%">: <?php echo $job['name'] ?></td>
      </tr>
      <tr>
        <td  width="5%">PHONE </td>
        <td>: <?php echo $job['phone'] ?></td>
      </tr>
      <tr>
        <td width="5%">E-MAIL</td>
        <td>: <?php echo $job['email'] ?></td>
      </tr>
      <tr>
        <td width="42%"></td>
        <td>&nbsp;</td>
      </tr>      
    </table>
  </div>
    <table width="100%" border="0" cellspacing="0" class="data-table">
      <tr>
        <td id="th_center" colspan="5" scope="col"><h3>Job Card</h3></td>
      </tr>      
      <tr>
        <th style="width: 5%">Sr#</th>  
        <th style="width: 20%">IMEI</th>
        <th style="width: 35%">Fault Description</th>
        <th style="width: 25%">Device Info </th>
        <th style="width: 15%">CHARGES</th>
      </tr>
<?php $total = 0.00; ?>      
<?php foreach ($job['items'] as $k => $device): ?>      
      <tr>
        <td><?php echo $k+1 ?></td>
        <td><span class="truncate"><?php echo $device['device_number'] ?></span></td>
        <td><span class="truncate"><?php echo strlen($device['fault_discription'])>60?substr($device['fault_discription'],0,57).'...':$device['fault_discription'] ?></span></td>
        <?php $devinfo = $device['brand'].' '.$device['model'].' ('.$device['color'].')'; ?>
        <td><span class="truncate"><?php echo strlen($devinfo)>40?substr($devinfo,0,37).'...':$devinfo ?></span></td>
        <td><strong>N$<?php echo number_format($device['amount'], 2) ?></strong></td>
      </tr>
<?php $total += floatval($device['amount']); ?>    
<?php endforeach ?>
      <tr>
        <td colspan="4" style="text-align: right;font-weight: bold;">Total Charges:</td>
        <td><strong>N$<?php echo number_format($total, 2) ?></strong></td>
      </tr>    
    </table>

  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="background-color:#FFF; vertical-align:top; padding-top:6px;">
        <div id="signcompny"></div>
        <div class="siglabel">For</div>
      </td>
      <td style="background-color:#FFF; vertical-align:top; padding-top:6px;">
        <div id="signcus"></div>
        <div class="siglabel">Customer</div>
      </td>
    </tr>
  </table>

  <div id="notices" style="clear:both; page-break-inside:avoid; margin-top:12px;">
    <h4 style="margin:0 0 6px 0; text-align:left;">Terms And Conditions</h4>
    <ol style="line-height:1.35; text-align:left; margin:6px 0 0 18px; font-size:12px;">
      <li>A non-refundable deposit fee of N$100 will be charged upon handing in a phone.</li>
      <li>Repairs carry a three month warranty. Liquid damage or no network shall be warranted on assessment.</li>
      <li>Phones not collected within 90 days will be sold to defray expenses.</li>
      <li>For security reasons, the phone cannot be collected without presenting this job card.</li>
      <li>Supatronix is not responsible for any data loss during the repair process. Customers are advised to back up their data before submitting their devices for repair.</li>
      <li>By handing in the above mentioned phone for repairs, I acknowledge and accept these terms and conditions.</li>
    </ol>
  </div>

</main>
<?php
$copy = ob_get_clean();
// Output two labeled copies side-by-side for printing on one page with a dashed cut line
$left = '<div class="copy"><div class="copy-label">Customer Copy</div>' . $copy . '</div>';
$right = '<div class="copy"><div class="copy-label">Shop Copy</div>' . $copy . '</div>';
echo '<div class="twoup">' . $left . '<div class="divider"></div>' . $right . '</div>';
?>
</body>
</html>
