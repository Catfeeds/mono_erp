<?php if (!defined('THINK_PATH')) exit();?><script src="/erptest/Application/Admin/View/Default/js/OrderDelivery/jquery-1.4.4.min.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/OrderDelivery/jquery-barcode.js"></script>
<style>
table 
{
border-top:1px solid #000000;
border-left:1px solid #000000;
width:100%;
border-collapse:collapse;
}
table tr td
{
border-bottom:1px solid #000000;
border-right:1px solid #000000;
padding:3px;
font-size:12px;
}
p
{
margin:0;
}
table tr td p
{
margin:3px 0 3px 0;
}
</style>

<p style="float:left; width:100%; font-size:30px; text-align:center; font-weight:bold; margin:10px 0 10px 0;">
Consignment Note
</p>
<table>
<tr>
<td style="width:40%;">
<span style="font-size:14px; font-weight:bold;">1.From(Collection Address)</span>
</td>
<td rowspan="8">
<table style="width:100%;">
<tr>
<td>
<p style="width:100%; text-align:center;font-size:38px; font-weight:bold;letter-spacing:0.8em; padding:15px 0 15px 0;">
TNT
</p>
<div style="width:100%; text-align:center; float:left;">
<div style="margin:auto;" id="bcTarget" class="barcodeImg"></div>
</div>
<div style="width:100%; text-align:center; float:left;margin:5px 0 5px 0;">
*<?php echo ($order_detail["trackingNumber_str"]); ?>*
</div>
<p style="width:100%; text-align:center; float:left;margin:5px 0 5px 0;">
Please quote this number if you have an enquiry.
</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">A.Delivery Address</span>
</td>
</tr>
<tr>
<td>
<p>Name:</p>
<p>Address:</p>
<p>City:</p>
<p>Province:</p>
<p>Contact Name:</p>
<p>Postal/Zip Code:</p>
<p>Country:</p>
<p>Tel No:</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">B.Dutiable Shipment Details</span>
</td>
</tr>
<tr>
<td>
Receivers VAT/TVA/BTW/MWST No.:
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">C.Special Delivery Instructions</span>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">D.Customer Reference</span>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">E.Invoice Receiver(Receiver's Account Number)</span>
</td>
</tr>
<tr>
<td>
NK
</td>
</tr>
<tr>
<td>
<p>
<span style="font-size:14px; font-weight:bold;">Receiverd by TNT</span>
</p>
<p style="float:left; width:100%;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">by(Name)</span>
<span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:300px;"></span>
</p>
<p style="float:left; width:100%;">
<p style="float:left; width:100%; margin:10px 0 10px 0;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Date : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0 35px 0 0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Time : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">:</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
</p>
</p>
<p>
Receiver's Copy
</p>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<p>
Sender's Account No : 3057324
</p>
<p>
Name : LILY MANUFACTURER
</p>
<p>
Address : NO.373 ZHUSHAN RD
</p>
<p>
City : NANJING-JIANGNING
</p>
<p>
Postal/Zip Code : 211100
</p>
<p>
Province : JIANGSU
</p>
<p>
Country : China
</p>
<p>
Contact Name : xiangyuan jin
</p>
<p>
Tel No : 02581034970
</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">2.To (Receiver Address)</span>
</td>
</tr>
<tr>
<td>
<p>
Name : <?php echo ($order_detail["name"]); ?>
</p>
<p>
Address : <?php echo ($order_detail["streetline"]); ?>
</p>
<p>
City : <?php echo ($order_detail["city"]); ?>
</p>
<p>
Postal/Zip Code : <?php echo ($order_detail["code"]); ?>
</p>
<p>
Province : <?php echo ($order_detail["state"]); ?>
</p>
<p>
Country : <?php echo ($order_detail["country"]); ?>
</p>
<p>
Contact Name : <?php echo ($order_detail["name"]); ?>
</p>
<p>
Tel No : <?php echo ($order_detail["phone"]); ?>
</p>
</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">3.Goods</span></td>
</tr>
<tr>
<td>
<p>
General Description : <?php echo ($order_detail["hs_name"]); ?>1pc
</p>
<p>
HS Tariff Code : <?php echo ($order_detail["hs_bm"]); ?>
</p>
<p>
Total Packages : 1
</p>
<p>
Total Weight : <?php echo ($order_detail["weight"]); ?>kg
</p>
<p>
Total Volume : 0.001 m<sup>3</sup>
</p>
</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">4.Services</span></td>
</tr>
<tr>
<td>
<p>
Service : Express
</p>
<p>
Option : <strong>Sender Pays</strong>          <span style="float:right;font-weight:bold;">NON DANGEROUS GOODS</span>
</p>
</td>
</tr>
<tr>
<td>
<p style="float:left; width:100%;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Sender's Signature : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:300px;"></span>
</p>
<p style="float:left; width:100%; margin:10px 0 10px 0;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Date : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
</p>
<p style="font-size:11px;">
TNT'S LIABLILY FOR LOSS,DAMAGE AND DELAY IS LIMITED BY THE CMR CONVENTION OR THE WARSAW CONVENTION WHICHEVER IS APPLICABLE. THE SENDER AGREES THAT THE GENERAL CONDITIONS , WHICH CAN BE VIEWED AT http://my.tnt.com/myTNT/footer/terms.do , ARE ACCEPTABLE AND GOVERN THIS CONTRACT.IF NO SERVICE OR BILLING OPTIONS ARE SELECTED THE FASTEST AVAILABLE SERVICE WILL BE CHARGED TO THE SENDER.
</p>
</td>
</tr>
</table>

<div  style="page-break-before:always;"><br /></div>

<table>
<tr>
<td>
<p><span style="font-size:14px; font-weight:bold;">Sender(Seller/Exporter)</span></p>
<p>
LILY MANUFACTURER
</p>
<p>
NO.373 ZHUSHAN RD
</p>
<p>
NANJING-JIANGNING
</p>
<p>
JIANGSU
</p>
<p>
211100
</p>
<p>
China
</p>
<p>
xiangyuan jin
</p>
<p>
02581034970
</p>
<p>
lj@monocn.com
</p>
</td>
<td>
<table>
<tr>
<td colspan="2"><p style="float:left;width:100%; font-size:25px;font-weight:bold; text-align:center;">Commercial Invoice</p></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Invoice Number:</span></td><td></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Shipping Date : </span></td><td><?php echo ($order_detail["date_time"]); ?></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Consignment Note No : </span></td><td><?php echo ($order_detail["trackingNumber_str"]); ?></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Purchase Order No:</span></td><td></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Invoice Currency:</span></td><td>USD</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Reason for Export:</span></td><td>Samples</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<p>
<span style="font-size:14px; font-weight:bold;">Receiver(Buyer/Importer)</span>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["streetline"]); ?>
</p>
<p>
<?php echo ($order_detail["city"]); ?>
</p>
<p>
<?php echo ($order_detail["code"]); ?>
</p>
<p>
<?php echo ($order_detail["state"]); ?>
</p>
<p>
<?php echo ($order_detail["country"]); ?>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["phone"]); ?>
</p>
</td>
<td valign="top">
<p>
<span style="font-size:14px; font-weight:bold;">DELIVER TO(if different form Receiver)</span>
</p>
</td>
</tr>
</table>
<br>
<table>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">Quantity</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Units</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Unit Weight(Kg)</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Weight(Kg)</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Description of Articles</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">HS Tariff Code</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Country of Origin</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Unit Value</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Value</span>
</td>
</tr>
<tr>
<td>1</td>
<td>piece</td>
<td><?php echo ($order_detail["weight"]); ?></td>
<td><?php echo ($order_detail["weight"]); ?>></td>
<td><?php echo ($order_detail["hs_name"]); ?></td>
<td><?php echo ($order_detail["hs_bm"]); ?></td>
<td>China</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="3">
<span style="font-size:14px; font-weight:bold;">Total Net Weight</span>
</td>
<td>
<?php echo ($order_detail["weight"]); ?>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Gross Weight:<?php echo ($order_detail["weight"]); ?></span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Number of Packages:1</span>
</td>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Total Article Value</span>
</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="6" rowspan="5">
<p><span style="font-size:14px; font-weight:bold;">Declaration(s)</span></p>
<p>
I declare that to the best of my knowledge the information on this invoice is true and correct
</p>
</td>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Discount</span>
</td>
<td>0.00</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Invoice Sub-total</span>
</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Freight Charges</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Insurance</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Other Charges</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">INCO Terms</span>
</td>
<td colspan="3">
Delivered Duty Paid(DDP)
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Credit Terms</span>
</td>
<td></td>
<td colspan="2"><span style="font-size:14px; font-weight:bold;">Invoice Total</span></td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
</table>
<br>
<table>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Shipper Name and Job Title</span></td>
<td><span style="font-size:14px; font-weight:bold;">Shipper Signature</span></td>
<td><span style="font-size:14px; font-weight:bold;">Date</span></td>
</tr>
<tr>
<td>xiangyuan jin - manger</td>
<td></td>
<td><?php echo ($order_detail["date_time"]); ?></td>
</tr>
</table>

<div  style="page-break-before:always;"><br /></div>


<p style="float:left; width:100%; font-size:30px; text-align:center; font-weight:bold; margin:10px 0 10px 0;">
Consignment Note
</p>
<table>
<tr>
<td style="width:40%;">
<span style="font-size:14px; font-weight:bold;">1.From(Collection Address)</span>
</td>
<td rowspan="8">
<table style="width:100%;">
<tr>
<td>
<p style="width:100%; text-align:center;font-size:38px; font-weight:bold;letter-spacing:0.8em; padding:15px 0 15px 0;">
TNT
</p>
<div style="width:100%; text-align:center; float:left;">
<div style="margin:auto;" id="bcTarget1" class="barcodeImg"></div>
</div>
<div style="width:100%; text-align:center; float:left;margin:5px 0 5px 0;">
*<?php echo ($order_detail["trackingNumber_str"]); ?>*
</div>
<p style="width:100%; text-align:center; float:left;margin:5px 0 5px 0;">
Please quote this number if you have an enquiry.
</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">A.Delivery Address</span>
</td>
</tr>
<tr>
<td>
<p>Name:</p>
<p>Address:</p>
<p>City:</p>
<p>Province:</p>
<p>Contact Name:</p>
<p>Postal/Zip Code:</p>
<p>Country:</p>
<p>Tel No:</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">B.Dutiable Shipment Details</span>
</td>
</tr>
<tr>
<td>
Receivers VAT/TVA/BTW/MWST No.:
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">C.Special Delivery Instructions</span>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">D.Customer Reference</span>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">E.Invoice Receiver(Receiver's Account Number)</span>
</td>
</tr>
<tr>
<td>
NK
</td>
</tr>
<tr>
<td>
<p>
<span style="font-size:14px; font-weight:bold;">Receiverd by TNT</span>
</p>
<p style="float:left; width:100%;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">by(Name)</span>
<span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:300px;"></span>
</p>
<p style="float:left; width:100%;">
<p style="float:left; width:100%; margin:10px 0 10px 0;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Date : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0 35px 0 0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Time : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">:</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
</p>
</p>
<p>
Receiver's Copy
</p>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<p>
Sender's Account No : 3057324
</p>
<p>
Name : LILY MANUFACTURER
</p>
<p>
Address : NO.373 ZHUSHAN RD
</p>
<p>
City : NANJING-JIANGNING
</p>
<p>
Postal/Zip Code : 211100
</p>
<p>
Province : JIANGSU
</p>
<p>
Country : China
</p>
<p>
Contact Name : xiangyuan jin
</p>
<p>
Tel No : 02581034970
</p>
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">2.To (Receiver Address)</span>
</td>
</tr>
<tr>
<td>
<p>
Name : <?php echo ($order_detail["name"]); ?>
</p>
<p>
Address : <?php echo ($order_detail["streetline"]); ?>
</p>
<p>
City : <?php echo ($order_detail["city"]); ?>
</p>
<p>
Postal/Zip Code : <?php echo ($order_detail["code"]); ?>
</p>
<p>
Province : <?php echo ($order_detail["state"]); ?>
</p>
<p>
Country : <?php echo ($order_detail["country"]); ?>
</p>
<p>
Contact Name : <?php echo ($order_detail["name"]); ?>
</p>
<p>
Tel No : <?php echo ($order_detail["phone"]); ?>
</p>
</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">3.Goods</span></td>
</tr>
<tr>
<td>
<p>
General Description : <?php echo ($order_detail["hs_name"]); ?>1pc
</p>
<p>
HS Tariff Code : <?php echo ($order_detail["hs_bm"]); ?>
</p>
<p>
Total Packages : 1
</p>
<p>
Total Weight : <?php echo ($order_detail["weight"]); ?>kg
</p>
<p>
Total Volume : 0.001 m<sup>3</sup>
</p>
</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">4.Services</span></td>
</tr>
<tr>
<td>
<p>
Service : Express
</p>
<p>
Option : <strong>Sender Pays</strong>          <span style="float:right;font-weight:bold;">NON DANGEROUS GOODS</span>
</p>
</td>
</tr>
<tr>
<td>
<p style="float:left; width:100%;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Sender's Signature : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:300px;"></span>
</p>
<p style="float:left; width:100%; margin:10px 0 10px 0;">
<span style="float:left; font-weight:bold; margin:0 10px 0 0;">Date : </span><span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span> <span style="float:left;">/</span> <span style="float:left; margin:0; height:18px; border-bottom:1px solid #000000; width:50px;"></span>
</p>
<p style="font-size:11px;">
TNT'S LIABLILY FOR LOSS,DAMAGE AND DELAY IS LIMITED BY THE CMR CONVENTION OR THE WARSAW CONVENTION WHICHEVER IS APPLICABLE. THE SENDER AGREES THAT THE GENERAL CONDITIONS , WHICH CAN BE VIEWED AT http://my.tnt.com/myTNT/footer/terms.do , ARE ACCEPTABLE AND GOVERN THIS CONTRACT.IF NO SERVICE OR BILLING OPTIONS ARE SELECTED THE FASTEST AVAILABLE SERVICE WILL BE CHARGED TO THE SENDER.
</p>
</td>
</tr>
</table>

<div  style="page-break-before:always;"><br /></div>

<table>
<tr>
<td>
<p><span style="font-size:14px; font-weight:bold;">Sender(Seller/Exporter)</span></p>
<p>
LILY MANUFACTURER
</p>
<p>
NO.373 ZHUSHAN RD
</p>
<p>
NANJING-JIANGNING
</p>
<p>
JIANGSU
</p>
<p>
211100
</p>
<p>
China
</p>
<p>
xiangyuan jin
</p>
<p>
02581034970
</p>
<p>
lj@monocn.com
</p>
</td>
<td>
<table>
<tr>
<td colspan="2"><p style="float:left;width:100%; font-size:25px;font-weight:bold; text-align:center;">Commercial Invoice</p></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Invoice Number:</span></td><td></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Shipping Date : </span></td><td><?php echo ($order_detail["date_time"]); ?></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Consignment Note No : </span></td><td><?php echo ($order_detail["trackingNumber_str"]); ?></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Purchase Order No:</span></td><td></td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Invoice Currency:</span></td><td>USD</td>
</tr>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Reason for Export:</span></td><td>Samples</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<p>
<span style="font-size:14px; font-weight:bold;">Receiver(Buyer/Importer)</span>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["streetline"]); ?>
</p>
<p>
<?php echo ($order_detail["city"]); ?>
</p>
<p>
<?php echo ($order_detail["code"]); ?>
</p>
<p>
<?php echo ($order_detail["state"]); ?>
</p>
<p>
<?php echo ($order_detail["country"]); ?>
</p>
<p>
<?php echo ($order_detail["name"]); ?>
</p>
<p>
<?php echo ($order_detail["phone"]); ?>
</p>
</td>
<td valign="top">
<p>
<span style="font-size:14px; font-weight:bold;">DELIVER TO(if different form Receiver)</span>
</p>
</td>
</tr>
</table>
<br>
<table>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">Quantity</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Units</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Unit Weight(Kg)</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Weight(Kg)</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Description of Articles</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">HS Tariff Code</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Country of Origin</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Unit Value</span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Value</span>
</td>
</tr>
<tr>
<td>1</td>
<td>piece</td>
<td><?php echo ($order_detail["weight"]); ?></td>
<td><?php echo ($order_detail["weight"]); ?></td>
<td><?php echo ($order_detail["hs_name"]); ?></td>
<td><?php echo ($order_detail["hs_bm"]); ?></td>
<td>China</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="3">
<span style="font-size:14px; font-weight:bold;">Total Net Weight</span>
</td>
<td>
<?php echo ($order_detail["weight"]); ?>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Gross Weight:<?php echo ($order_detail["weight"]); ?></span>
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Total Number of Packages:1</span>
</td>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Total Article Value</span>
</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="6" rowspan="5">
<p><span style="font-size:14px; font-weight:bold;">Declaration(s)</span></p>
<p>
I declare that to the best of my knowledge the information on this invoice is true and correct
</p>
</td>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Discount</span>
</td>
<td>0.00</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Invoice Sub-total</span>
</td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Freight Charges</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Insurance</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td colspan="2">
<span style="font-size:14px; font-weight:bold;">Other Charges</span>
</td>
<td>
0.00
</td>
</tr>
<tr>
<td>
<span style="font-size:14px; font-weight:bold;">INCO Terms</span>
</td>
<td colspan="3">
Delivered Duty Paid(DDP)
</td>
<td>
<span style="font-size:14px; font-weight:bold;">Credit Terms</span>
</td>
<td></td>
<td colspan="2"><span style="font-size:14px; font-weight:bold;">Invoice Total</span></td>
<td><?php echo ($order_detail["hs_price"]); ?></td>
</tr>
</table>
<br>
<table>
<tr>
<td><span style="font-size:14px; font-weight:bold;">Shipper Name and Job Title</span></td>
<td><span style="font-size:14px; font-weight:bold;">Shipper Signature</span></td>
<td><span style="font-size:14px; font-weight:bold;">Date</span></td>
</tr>
<tr>
<td>xiangyuan jin - manger</td>
<td></td>
<td><?php echo ($order_detail["date_time"]); ?></td>
</tr>
</table>


<script>

$("#bcTarget").empty().barcode("<?php echo ($order_detail["trackingNumber_str"]); ?>", "code128",{barWidth:2, barHeight:70,showHRI:false});

$("#bcTarget1").empty().barcode("<?php echo ($order_detail["trackingNumber_str"]); ?>", "code128",{barWidth:2, barHeight:70,showHRI:false});

window.onload=function()
{
   window.print();
}
</script>