<?php if (!defined('THINK_PATH')) exit();?><HTML>
<object id="WebBrowser" width=0 height=0 classid="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2">
</object>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta content="en" http-equiv="Content-Language">
<style>
.fenye{page-break-after: always}
.my_page tr{border-bottom:5px solid red;}
.my_page td{padding-left:10px;padding-top:30px;padding-bottom:10px;}
.my_page p{font-size:20px;}
</style>
<style type="text/css">
	.printedpackingslip p, .printedpackingslip span, .printedpackingslip td, .printedpackingslip a {font-family: Arial, Helvetica, sans-serif;}
    body{padding:2em;}
	div.printbucket {margin-top:10pt;}

    .content-area {
        margin: 0;
        padding: 0;
        width: 100%;
    }

	.printedtable {
		border:1px solid #000000;
		}
	.printedtable th.leftcolumn, .printedtable td.leftcolumn{
		background-color:#ffffff;
		padding:7px;
		vertical-align:top;
		border-left:0px solid #aaaaaa;

		}
	.printedtable td, .printedtable th {
		background-color:#ffffff;
		padding:7px;
		vertical-align:top;
		border-left:1px solid #aaaaaa;
		}
	.printedtable th {
		font-weight:bold;
		vertical-align:middle;
		font-size:22px;
		}
	.printedtable tr.shippeditem td {
		border-top:1px solid #000000;
		}

	.printedsubtext {font-size:22px;}
	.printednormaltext {font-size:25px;}
	.printedlargetext {font-size:30px;}
	.printedlargesttext {font-size:40px;}

    .printedpageline {
        padding:0;
        margin:40px 0;
        border:2px solid #000;
    }

    .printedpagebreak {
        page-break-after:always;
    }

    .printedtable tr.shippeditem .printedtotal td {
            border:0px;
            padding:0px 3px 3px 3px;
            }
</style>

<style type="text/css" media="print">
    .printedpageline { display:none; }
</style>
</head>
<body>
<?php if(is_array($info)): $k = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($vo['come_from_info']['style']=="web"){ ?>
<!-- 网站订单 -->
<?php if($vo['come_from_info']['name'] =="fr") { $word_chenhu="Chère cliente, cher client,"; $word_xieci="Merci beaucoup de votre achat chez Lilysilk! Pour évaluer nos produits et services, veuillez vous rendre sur notre site."; $word_shipping="Adresse de Livraison:"; $word_chakan="Nous vous remercions par avance pour votre retour."; $word_o_num="Numéro de la commande"; $word_num="Quantité"; $word_name="Détails sur le produit"; $word_model="modèle"; } elseif($vo['come_from_info']['name']=="de") { $word_chenhu="Lieber Kunde,"; $word_xieci="Vielen Dank für Ihren Einkauf bei Lilysilk. Wir hoffen, dass Sie damit zufrieden sind. Es wäre sehr nett, wenn Sie Ihre Bestellung Liste wie folgen überprüfen:"; $word_shipping="Lieferanschrift:"; $word_chakan="Wir freuen uns auf Ihren nächsten Einkauf."; $word_o_num="Bestellnummer"; $word_num="Menge"; $word_name="Produktdetails"; $word_model="Artikelnummer"; } elseif($vo['come_from_info']['name']=="nl") { $word_xieci="BedankT voor uw aankoop op Lilysilk. We hopen dat u tevreden met onze producten en service bent. Controleer de volgende bestellijst alstublieft."; $word_shipping="Verzendingadres:"; $word_chakan="Verwacht uw volgende bezoek."; $word_o_num="Bestelnummer"; $word_num="AANTAL"; $word_name="Productnaam"; $word_model="Artikelnummer"; } elseif($vo['come_from_info']['name']=="jp") { $word_chenhu="様  ".":"; $word_xieci="LilySilkでお買い上げいただき、誠にありがとうございます。お客様の注文が完了いたしましたのでご連絡申し上げます。注文内容："; $word_shipping="送付先住所:"; $word_chakan="またのご利用を心よりお待ちしております。"; $word_o_num="注文番号"; $word_num="数量"; $word_name="商品名"; $word_model="model"; } else { if($vo['shipping_info']) { $word_chenhu="Dear ".$vo['shipping_info']['first_name']." ".$vo['shipping_info']['last_name'].":"; } else { $word_chenhu="Dear ".$vo['first_name']."&nbsp;".$vo['last_name'].":"; } $word_xieci="Thank you for your purchase from Lilysilk. We do hope you will be satisfied with our products and service. Please check the following order list."; $word_shipping="Shipping Address:"; $word_chakan="Looking forward for your next visit."; $word_o_num="Order Number"; $word_num="QTY"; $word_name="Product Name"; $word_model="model"; } $isset_wash_tenestar=false; ?>
	<div style="padding:10px; width:1000px;margin:auto;height:auto;" class="my_page">
        <p style="margin:0;">
        <img style="border:none; width:340px;" src="http://www.lilysilk.com/mono_images/common/logo.png" title="lilysilk" alt="lilysilk">
        </p>	
        <div style="width:100%; float:left;">	
            <div style="font-size:30px; color:#000; width:45%;text-align:left; float:left; margin-right:20px;">	
                <p style="font-size:30px;"><span style=""><?php echo ($word_chenhu); ?> </span></p>	
                <p style="font-size:30px;">	
                    <?php echo ($word_xieci); ?>	
                </p>	
            </div>	
            <div style="font-size:30px; text-align:left; width:30%; float:right; padding-top:30px;">	
                <span style="font-weight:bold;color:#C63;"><?php echo ($word_shipping); ?></span><br>	
                <?php echo ($vo['order_web_address'][0]['first_name']); ?>&nbsp;<?php echo ($vo['order_web_address'][0]['last_name']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['address']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['city']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['province']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['country']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['code']); ?><br>	
                <?php echo ($vo['order_web_address'][0]['telephone']); ?><br>
            </div>
		</div>
        <p style="font-size:32px;"><?php echo ($word_chakan); ?>	</p>	
        <div style="width:100%;  text-align:left; border:1px solid #000;">	
            <p style="font-size:30px;font-weight:bold;padding-bottom:15px;padding-left:10px;border-bottom:1px solid #000;">
            	<?php echo $word_o_num.":";?>	<?php echo str_replace('-','0',substr($vo['date_time'],0,10))."0".$vo['customer_id']."0".$vo['order_number'];?>&nbsp;&nbsp;
                <?php if(is_array($vo['factory_order'])): $i = 0; $__LIST__ = $vo['factory_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$factory_order_vo): $mod = ($i % 2 );++$i; if($factory_order_vo['order_platform_id'] != 0) { $order_fac_type = 'plat'; } elseif($factory_order_vo['order_id'] !=0) { $order_fac_type = 'web'; } ?>
                &nbsp;&nbsp; <?php echo get_factory_str($factory_order_vo['factory_id'],$factory_order_vo['date'],$factory_order_vo['number'],'execl',$order_fac_type); endforeach; endif; else: echo "" ;endif; ?>
                
                <?php if(is_array($vo['fba_order'])): $i = 0; $__LIST__ = $vo['fba_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fba_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['orderplatform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
                
                &nbsp;&nbsp; FBA<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($fba_order_vo["date"]))); ?>-<?php if($fba_order_vo["number"] < 10): ?>0<?php endif; echo ($fba_order_vo["number"]); endforeach; endif; else: echo "" ;endif; ?>
                
                <?php if(is_array($vo['product_stock_order'])): $i = 0; $__LIST__ = $vo['product_stock_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_stock_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['order_platform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
                &nbsp;&nbsp; K<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($product_stock_order_vo["date"]))); ?>-<?php if($product_stock_order_vo["number"] < 10): ?>0<?php endif; echo ($product_stock_order_vo["number"]); endforeach; endif; else: echo "" ;endif; ?>
                &nbsp;&nbsp;&nbsp;Lilysilk_<?php echo ($vo["come_from_info"]["name"]); ?>
            </p>
            <table style="width:100%;font-size:20px;">
            <tr>
                <th style="width:60%;font-size:28px;padding-left:10px;" align="left"><?php echo ($word_name); ?></th>
                <th style="width:20%;font-size:28px;" align="left"><?php echo ($word_num); ?></th>
            </tr>

            <?php if($vo['product_customization_info']): if(is_array($vo['product_customization_info'])): $i = 0; $__LIST__ = $vo['product_customization_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_customization): $mod = ($i % 2 );++$i;?><tr>
            	<td>
                <p style="font-size: 32px"><?php echo ($product_customization[name]); ?></p>
                <?php echo ($product_customization[description]); ?>
                </td>
                <td>
                
                </td>
             </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(is_array($vo['product_original_info'])): $i = 0; $__LIST__ = $vo['product_original_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($i % 2 );++$i; $continue_volist=false; ?>
             <?php if(is_array($vo['order_web_product'])): $i = 0; $__LIST__ = $vo['order_web_product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$order_web_product): $mod = ($i % 2 );++$i; if($order_web_product[order_product_id] == $product_vo[order_product_id]): if($order_web_product[status]=="cancel") { $continue_volist=true; } endif; endforeach; endif; else: echo "" ;endif; ?>
             <?php
 if($continue_volist) { continue; } ?>
            <?php
 if(strtolower($product_vo[product_name])=="special silk washing detergent tenestar" && $vo['come_from_info']['name']=="us") { $isset_wash_tenestar=true; } ?>
			<tr>
			 <?php if($product_vo['product_set_name'] == ''): ?><td>
                    <p style="font-size: 32px">
                       <?php echo ($product_vo['product_name']); ?>
                     </p>
                    <p class="product_color">
                        <?php echo ($product_vo['color']); ?>
                        </p>
                    <p>
                    	 <?php echo ($product_vo['size']); ?>
                          <?php if(is_array($vo['order_web_product'])): $i = 0; $__LIST__ = $vo['order_web_product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$order_web_product): $mod = ($i % 2 );++$i; if($order_web_product[order_product_id] == $product_vo[order_product_id]): if(!empty($order_web_product[nightwear_customization_info])): switch($vo['come_from_info']['name']): case "jp": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("身長","体重","胸周り","ウェスト","尻周り","パンツ丈","袖丈"),$order_web_product[nightwear_customization_info][customization]); break;?>
								    <?php case "fr": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Taille","Poids","Tour de poitrine","Tour de taille","Tour de bassin","Longueur du bas","Longueur de manche"),$order_web_product[nightwear_customization_info][customization]); break;?>
								    <?php case "de": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Höhe","Gewicht","Büste","Taille","Hüften","Beinlänge","Ärmel"),$order_web_product[nightwear_customization_info][customization]); break;?>
								     <?php case "nl": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Hoogte","Gewicht","Borst","Taille","heupen","Beenlengte","Mouw"),$order_web_product[nightwear_customization_info][customization]); break;?>
								     <?php case "it": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Altezza","Peso","Busto","Cintura","Fianchi","Lunghezza della gamba","Manica"),$order_web_product[nightwear_customization_info][customization]); break;?>
								     <?php case "es": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Altura","Peso","busto","cintura","cadera","Longitud de las piernas","manga"),$order_web_product[nightwear_customization_info][customization]); break;?>
								     <?php case "ru": echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("pост","вес","окружность груди","талия","бедра","длина ног","длина рукава"),$order_web_product[nightwear_customization_info][customization]); break;?>
								    <?php default: echo str_replace(array("身高","体重","胸围","腰围","臀围","腿长","袖长"),array("Height","Weight","Bust/Chest","Waist","Hips","Length","Sleeve"),$order_web_product[nightwear_customization_info][customization]); endswitch; endif; endif; endforeach; endif; else: echo "" ;endif; ?>                      
                          
                    </p>
                    <?php
 if($product_vo['sku']!=''){ ?>
                    <p>model : <?php echo ($product_vo['sku']); ?></p>
                    <?php
 } ?>
                </td>
                <td>
                    <span><?php echo ($product_vo['number']); ?></span>
                </td>
                <td style="text-align:center;">
	              <?php if(is_array($vo['order_web_product'])): $i = 0; $__LIST__ = $vo['order_web_product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$order_web_product): $mod = ($i % 2 );++$i; if($order_web_product[order_product_id] == $product_vo[order_product_id]): if($order_web_product[extra_info]['gift_box']!='') { ?>
		              		<img src='<?php echo ($order_web_product[extra_info]['gift_box']); ?>'>
		              	<?php
 } if($order_web_product[extra_info]['gramming_img']!='') { ?>
						<img src='<?php echo ($order_web_product[extra_info]['gramming_img']); ?>'>
						<br>
						<?php echo ($order_web_product[extra_info]['gramming_name']); ?>
						<br>
						<?php echo ($order_web_product[extra_info]['gramming_color']); ?>
						<?php
 } endif; endforeach; endif; else: echo "" ;endif; ?>           
                </td>
             <?php else: ?>
                 <td>
                 <p style="font-size: 32px">
                       <?php echo ($product_vo['product_set_name']); ?>&nbsp;&nbsp;<?php echo ($product_vo['color']); ?>&nbsp;&nbsp;<?php echo ($product_vo['size']); ?>
                  </p>
                  <?php if(is_array($product_vo['set_info'])): $i = 0; $__LIST__ = $product_vo['set_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_set_one): $mod = ($i % 2 );++$i;?><p class="product_one_name" style="width: 100%;display: block;clear: both;float:left;margin:10px 0;"><span style="width:500px;display:block;float:left;margin-right:30px;"><?php echo ($product_set_one['product_name']); ?></span><span style="width:400px;display:block;float:left;margin-right:30px;"><?php echo ($product_set_one['size']); ?></span><span style="width:100px;display:block;float:left;"><?php echo ($product_set_one['number']); ?></span></p><?php endforeach; endif; else: echo "" ;endif; ?>
                  </td><?php endif; ?>
                
            </tr>
              <?php if(is_array($vo['order_web_product'])): $i = 0; $__LIST__ = $vo['order_web_product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$order_web_product): $mod = ($i % 2 );++$i; if($order_web_product[order_product_id] == $product_vo[order_product_id]): if($order_web_product['extra_info']['gift_product_name']!="") { ?>
	              		<tr>
	              		<td class='name'>
	              		<?php echo ($order_web_product[extra_info]['gift_name']); ?><br><?php echo ($order_web_product[extra_info]['gift_product_name']); ?>
	              		</td>
	              		<td>1</td>
	              		<td></td>
	              		</tr>
	              <?php
 } endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
            <!--
            <tr>	
                <td class="name">
                    Gift<br>
                    Silk Sleep Eye Mask With Wide Elastic Band	
                </td>	
                <td>
                    1
                </td>			
                <td>&nbsp;</td>
            </tr>-->
            </table>
        </div>
	</div>
    <?php  if($isset_wash_tenestar) { ?>
		<p style="text-align:right;font-size:25px;width:1200px;margin:auto;text-align:left;">
		<span style="font-size:58px;">☆</span>Please note that, the Tenestar detergent in your order will be shipped out separately  for the protection of your delicate silk items. We apologize for any inconvenience this may cause and thank you for your patience with us. 
		</p>
		<?php  } else { ?>
		   <p>
		   </p>
	<?php  } ?>
    <p <?php if($info_num > $k): ?>class="fenye"<?php endif; ?> style="text-align:right;font-size:25px;width:1000px;margin:auto;">www.lilysilk.com</p>
<?php } else { if($vo['come_from_info']['id']==17 || $vo['come_from_info']['id']==18 || $vo['come_from_info']['id']==19 || $vo['come_from_info']['id']==20 || $vo['come_from_info']['id']==101){ ?>

<!-- 平台，非亚马逊订单 -->
<?php if($vo['come_from_info']['id']=="26") { $word_chenhu="Chère cliente, cher client,"; $word_xieci="Merci beaucoup de votre achat chez Lilysilk! Pour évaluer nos produits et services, veuillez vous rendre sur notre site."; $word_shipping="Adresse de Livraison:"; $word_chakan="Nous vous remercions par avance pour votre retour."; $word_o_num="Numéro de la commande"; $word_num="Quantité"; $word_name="Détails sur le produit"; $word_model="modèle"; } elseif($vo['come_from_info']['id']=="24") { $word_chenhu="Lieber Kunde,"; $word_xieci="Vielen Dank für Ihren Einkauf bei Lilysilk. Wir hoffen, dass Sie damit zufrieden sind. Es wäre sehr nett, wenn Sie Ihre Bestellung Liste wie folgen überprüfen:"; $word_shipping="Lieferanschrift:"; $word_chakan="Wir freuen uns auf Ihren nächsten Einkauf."; $word_o_num="Bestellnummer"; $word_num="Menge"; $word_name="Produktdetails"; $word_model="Artikelnummer"; } elseif($vo['come_from']=="nl") { $word_xieci="BedankT voor uw aankoop op Lilysilk. We hopen dat u tevreden met onze producten en service bent. Controleer de volgende bestellijst alstublieft."; $word_shipping="Verzendingadres:"; $word_chakan="Verwacht uw volgende bezoek."; $word_o_num="Bestelnummer"; $word_num="AANTAL"; $word_name="Productnaam"; $word_model="Artikelnummer"; } elseif($vo['come_from_info']['id']=="28" || $vo['come_from_info']['id']=="20") { $word_chenhu="様  ".":"; $word_xieci="LilySilkでお買い上げいただき、誠にありがとうございます。お客様の注文が完了いたしましたのでご連絡申し上げます。注文内容："; $word_shipping="送付先住所:"; $word_chakan="またのご利用を心よりお待ちしております。"; $word_o_num="注文番号"; $word_num="数量"; $word_name="商品名"; $word_model="model"; } else { if($vo['shipping_info']) { $word_chenhu="Dear ".$vo['shipping_info']['name'].":"; } else { $word_chenhu="Dear ".$vo['customer_info']['first_name']."&nbsp;".$vo['customer_info']['last_name'].":"; } $word_xieci="Thank you for your purchase from Lilysilk. We do hope you will be satisfied with our products and service. Please check the following order list."; $word_shipping="Shipping Address:"; $word_chakan="Looking forward for your next visit."; $word_o_num="Order Number"; $word_num="QTY"; $word_name="Product Name"; $word_model="model"; } ?>
	<div style="padding:10px; width:1200px;margin:auto;height:auto;" class="my_page">
        <p style="margin:0;">
        <img style="border:none; width:340px;" src="http://www.lilysilk.com/mono_images/common/logo.png" title="lilysilk" alt="lilysilk">
        </p>	
        <div style="width:100%; float:left;">	
            <div style="font-size:30px; color:#000; width:45%;text-align:left; float:left; margin-right:20px;">	
                <p style="font-size:30px;"><span style=""><?php echo ($word_chenhu); ?> </span></p>	
                <p style="font-size:30px;">	
                    <?php echo ($word_xieci); ?>	
                </p>	
            </div>	
            <div style="font-size:30px; text-align:left; width:30%; float:right; padding-top:30px;">	
                <span style="font-weight:bold;color:#C63;"><?php echo ($word_shipping); ?></span><br>	
                <?php echo ($vo['shipping_info']['name']); ?><br>	
                <?php echo ($vo['shipping_info']['address1']); ?>&nbsp;<?php echo ($vo['shipping_info']['address2']); ?>&nbsp;<?php echo ($vo['shipping_info']['address3']); ?><br>	
                <?php echo ($vo['shipping_info']['city']); ?><br>	
                <?php echo ($vo['shipping_info']['state']); ?><br>	
                <?php echo ($vo['shipping_info']['country']); ?><br>	
                <?php echo ($vo['shipping_info']['post']); ?><br>	
                <?php echo ($vo['shipping_info']['telephone']); ?>
            </div>
		</div>
        <p style="font-size:32px;"><?php echo ($word_chakan); ?>	</p>	
        <div style="width:100%;  text-align:left; border:1px solid #000;">	
            <p style="font-size:30px;font-weight:bold;padding-bottom:15px;padding-left:10px;border-bottom:1px solid #000;">
            	<?php echo ($word_o_num); ?>: 	<?php echo str_replace('-','0',$vo['order_number'])?>&nbsp;&nbsp;&nbsp;
                <?php if(is_array($vo['factory_order'])): $i = 0; $__LIST__ = $vo['factory_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$factory_order_vo): $mod = ($i % 2 );++$i; if($factory_order_vo['order_platform_id'] != 0) { $order_fac_type = 'plat'; } elseif($factory_order_vo['order_id'] !=0) { $order_fac_type = 'web'; } ?>
                &nbsp;&nbsp; <?php echo get_factory_str($factory_order_vo['factory_id'],$factory_order_vo['date'],$factory_order_vo['number'],'execl',$order_fac_type); endforeach; endif; else: echo "" ;endif; ?>
                
                <?php if(is_array($vo['fba_order'])): $i = 0; $__LIST__ = $vo['fba_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fba_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['orderplatform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
                
                &nbsp;&nbsp; FBA<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($fba_order_vo["date"]))); ?>-<?php if($fba_order_vo["number"] < 10): ?>0<?php endif; echo ($fba_order_vo["number"]); endforeach; endif; else: echo "" ;endif; ?>
                
                <?php if(is_array($vo['product_stock_order'])): $i = 0; $__LIST__ = $vo['product_stock_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_stock_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['order_platform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
                &nbsp;&nbsp; K<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($product_stock_order_vo["date"]))); ?>-<?php if($product_stock_order_vo["number"] < 10): ?>0<?php endif; echo ($product_stock_order_vo["number"]); endforeach; endif; else: echo "" ;endif; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lilysilk_<?php echo ($vo["come_from_info"]["name"]); ?>
            </p>
            <table style="width:100%;font-size:20px;">
            <tr>
                <th style="width:60%;font-size:28px;padding-left:10px;" align="left"><?php echo ($word_name); ?></th>
                <th style="width:20%;font-size:28px;" align="left"><?php echo ($word_num); ?></th>
            </tr>
            
            <?php if(is_array($vo['product_info'] )): $i = 0; $__LIST__ = $vo['product_info'] ;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
                    <p style="font-size: 32px">
                      <?php echo ($product_vo["name"]); ?>
                     </p>
                     <?php if(!empty($product_vo['rakuten_product_color'])): ?><p class="product_color">
                       	<?php echo ($product_vo['rakuten_product_color']); ?>
                  		 </p><?php endif; ?>
                    <?php if(!empty($product_vo['rakuten_product_size'])): ?><p>
	                    	<?php echo ($product_vo['rakuten_product_size']); ?>
	                    </p><?php endif; ?>
                    <?php  if($product_vo[sku]){ ?>
                    <p>SKU : <?php echo ($product_vo["sku"]); ?></p>
                    <?php
 } ?>
                </td>
                <td>
                    <span><?php echo ($product_vo['number']); ?></span>
                </td>
                <td style="text-align:center;">
                                
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            <!--
            <tr>	
                <td class="name">
                    Gift<br>
                    Silk Sleep Eye Mask With Wide Elastic Band	
                </td>	
                <td>
                    1
                </td>			
                <td>&nbsp;</td>
            </tr>-->
            </table>
        </div>
	</div>
    <p></p>
    <p <?php if($info_num > $k): ?>class="fenye"<?php endif; ?> style="text-align:right;font-size:25px;width:1200px;margin:auto;">www.lilysilk.com</p>
<?php } else { ?>
<!-- 亚马逊订单 -->
<?php
$ship_to_word="Ship To"; $order_id_word="Order ID"; $thank_word="Thank you for buying from LILYSILK on Amazon Marketplace."; $shipping_adress_word="Shipping Address"; $order_date_word="Order Date"; $shipping_service_word="Shipping Service"; $buyer_name_word="Buyer Name"; $seller_name_word="Seller Name"; $quantity_word="Quantity"; $product_details_word="Product Details"; $price_word="Price"; $total_word="Total"; $total_td_word=$total_word; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Listing ID"; $order_item_id_word="Order Item ID"; $condition_word="Condition"; $subtotal_word="Subtotal"; $promotions_word="Promotions"; $order_total_word="ORDER TOTAL"; $thanks_line='<b>Thanks for buying on Amazon Marketplace</b>. To provide feedback for the seller please visit www.amazon.com/feedback. To contact the seller, go to Your Orders in Your Account. Click the seller\'s name under the appropriate product. Then, in the "Further Information" section, click "Contact the Seller."'; ?>
<?php if($vo[come_from_id] == 2): $thanks_line='<b>Thanks for buying on Amazon Marketplace</b>. To provide feedback for the seller please visit www.amazon.ca/feedback. To contact the seller, go to Your Orders in Your Account. Click the seller\'s name under the appropriate product. Then, in the "Further Information" section, click "Contact the Seller." '; ?>
<?php elseif($vo[come_from_id] == 23): ?>
<?php
$ship_to_word="Dispatch to"; $thank_word="Thank you for buying from LilySilkUK on Amazon Marketplace."; $shipping_adress_word="Delivery address"; $shipping_service_word="Delivery Service"; $thanks_line='<b>Thanks for buying on Amazon Marketplace</b>. To provide feedback for the seller please visit www.amazon.co.uk/feedback. To contact the seller, go to Your Orders in Your Account. Click the seller\'s name under the appropriate product. Then, in the "Further Information" section, click "Contact the Seller." '; ?>
<?php elseif($vo[come_from_id] == 27): ?>
<?php
$ship_to_word="Spedire a"; $order_id_word="Numero dell'ordine"; $thank_word="Ti ringraziamo per aver acquistato da LilySilkUK su Amazon. "; $shipping_adress_word="Indirizzo di spedizione"; $order_date_word="Data ordine"; $shipping_service_word="Tipo di spedizione"; $buyer_name_word="Nome acquirente"; $seller_name_word="Nome venditore"; $quantity_word="Quantità"; $product_details_word="Dettagli prodotto"; $price_word="Prezzo"; $total_word="Totale"; $total_td_word=$total_word; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Numero offerta"; $order_item_id_word="Numero articolo ordine"; $condition_word="Condizione"; $subtotal_word="Subtotale"; $promotions_word="Promozioni"; $order_total_word="Totale ordine"; $thanks_line='<b>Grazie per aver comprato nel Marketplace di Amazon</b>. Per fornire il tuo feedback sul venditore, visita la pagina seguente: www.amazon.it/feedback. Se desideri contattare il venditore, seleziona il link “Il mio account”, che trovi in alto a destra su ogni pagina di Amazon.it, e accedi alla sezione “I miei ordini”. Individua l\'ordine in questione e clicca su “Contatta il venditore”. '; ?>
<?php elseif($vo[come_from_id] == 26): ?>
<?php
$ship_to_word="Adresse d'expédition"; $order_id_word="Numéro de la commande"; $thank_word="Merci pour votre achat avec le vendeur LilySilkFR sur Amazon.fr "; $shipping_adress_word="Adresse d'expédition"; $order_date_word="Date de commande"; $shipping_service_word="Service de livraison"; $buyer_name_word="Nom de l'acheteur"; $seller_name_word="Nom du vendeur"; $quantity_word="Quantité"; $product_details_word="Détails sur le produit"; $price_word="Prix"; $total_word="Total"; $total_td_word=$total_word; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Référence de l'offre"; $order_item_id_word="ID commande-article"; $condition_word="Etat"; $subtotal_word="Sous-total"; $promotions_word="les promotions"; $order_total_word="TOTAL COMMANDE"; $thanks_line='<b>Merci de votre achat sur Amazon Marketplace</b>. Pour évaluer votre vendeur, veuillez vous rendre sur www.amazon.fr/feedback. Pour contacter votre vendeur, veuillez cliquez sur « Votre compte » puis « Vos commandes ». Localisez la commande puis cliquez sur « Contactez le vendeur » à droite du produit. '; ?>
<?php elseif($vo[come_from_id] == 3): ?>
<?php
$ship_to_word="Enviar a"; $order_id_word="Id. del pedido"; $thank_word="Gracias por comprar a LILYSILK en Amazon Marketplace."; $shipping_adress_word="Shipping Address"; $order_date_word="Fecha del pedido"; $shipping_service_word="Servicio de envío"; $buyer_name_word="Nombre del comprador"; $seller_name_word="Nombre del vendedor"; $quantity_word="Cantidad"; $product_details_word="Detalles del producto"; $price_word="Precio"; $total_word="Total"; $total_td_word="Envío"; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Identificador del listado"; $order_item_id_word="Identificador del listado"; $condition_word="Estado"; $subtotal_word="Subtotal"; $promotions_word="Promociones"; $order_total_word="TOTAL DEL PEDIDO"; $thanks_line='<b>Gracias por comprar en Amazon Marketplace</b>. Para brindar comentarios al vendedor, visita www.amazon.com.mx/feedback. Para ponerse en contacto con el vendedor, visita Amazon.com y haz clic en "Tu cuenta" en la parte superior de cualquier página. En Tu cuenta, ve a la sección "Pedidos" y haz clic en el enlace "Dejar comentarios sobre el vendedor". Selecciona el pedido y haz clic en el botón "Ver pedido". Haz clic en el "perfil del vendedor" debajo del producto adecuado. En la sección inferior derecha de la página, debajo de "Ayuda para el vendedor", haz clic en "Ponerse en contacto con este vendedor".'; ?>
<?php elseif($vo[come_from_id] == 25): ?>
<?php
$ship_to_word="Enviar a"; $order_id_word="Nº de pedido"; $thank_word="Gracias por comprar en LilySilkUK en Marketplace de Amazon. "; $shipping_adress_word="Dirección de envío"; $order_date_word="Fecha del pedido"; $shipping_service_word="Servicio de envío"; $buyer_name_word="Nombre del comprador"; $seller_name_word="Nombre del vendedor"; $quantity_word="Cantidad"; $product_details_word="Detalles del producto"; $price_word="Precio"; $total_word="Total"; $total_td_word=$total_word; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Identificador del listing"; $order_item_id_word="N.º del artículo encargado"; $condition_word="Estado"; $subtotal_word="Subtotal"; $promotions_word="Promociones"; $order_total_word="Importe total del pedido"; $thanks_line='<b>Gracias por comprar en Amazon Marketplace</b>. Para valorar al vendedor, visita la siguiente página: www.amazon.es/feedback<br> Si quieres ponerte en contacto con el vendedor, accede a “Mi cuenta” (en la parte superior derecha de tu cuenta de Amazon) y haz clic en “Mis pedidos”. Localiza el producto en cuestión y haz clic en “Contactar con el vendedor”.'; ?>
<?php elseif($vo[come_from_id] == 24): ?>
<?php
$ship_to_word="Liefern an"; $order_id_word="Bestellnummer"; $thank_word="Vielen Dank für Ihren Einkauf bei LilySilkUK auf Amazon.de Marketplace. "; $shipping_adress_word="Lieferanschrift"; $order_date_word="Bestellt am"; $shipping_service_word="Versandart"; $buyer_name_word="Name des Käufers"; $seller_name_word="Name des Verkäufers"; $quantity_word="Menge"; $product_details_word="Produktdetails"; $price_word="Preis"; $total_word="Gesamt"; $total_td_word="Summe"; $sku_word="SKU"; $asin_word="ASIN"; $listing_id_word="Angebotsnummer"; $order_item_id_word="Auftrags-Artikelnr."; $condition_word="Zustand"; $subtotal_word="Zwischensumme"; $promotions_word="beförderungen"; $order_total_word="SUMME DER BESTELLUNG"; $thanks_line='<b>Vielen Dank für Ihren Einkauf bei Amazon Marketplace</b>! Unter www.amazon.de/feedback können Sie Ihre Bewertung für diesen Verkäufer abgeben. Wenn Sie den Verkäufer kontaktieren möchten, gehen Sie über "Mein Konto" auf "Meine Bestellungen". Suchen Sie die Bestellung und klicken Sie dort bitte auf die Schaltfläche "Verkäufer kontaktieren" um zum Kontaktformular zu gelangen.'; endif; ?>
<div class="printedpackingslip">
<div style="margin: 0 auto 25pt auto;">
  <table border="0" cellpadding="1" cellspacing="0" width="100%" align="center">
    <tbody><tr>
      <td align="left" valign="top" width="10%" class="printednormaltext" style="padding-top:3pt;">
        <?php echo ($ship_to_word); ?>:
      </td>
    </tr>
    <tr>
      <td align="left" width="65%" class="printedlargesttext">
          <strong>
          <?php echo ($vo['shipping_info']['name']); ?><br>
                <?php echo ($vo['shipping_info']['address1']); ?>&nbsp;<?php echo ($vo['shipping_info']['address2']); ?>&nbsp;<?php echo ($vo['shipping_info']['address3']); ?><br>	
                <?php echo ($vo['shipping_info']['city']); ?><br>	
                <?php echo ($vo['shipping_info']['state']); ?><br>	
                <?php echo ($vo['shipping_info']['country']); ?><br>	
                <?php echo ($vo['shipping_info']['post']); ?><br>
                </strong>
      </td>
    </tr>
  </tbody></table>
</div>
<!-- Dashed separator -->
<div style="border-top: 1px dashed rgb(0, 0, 0); margin: 20px 0pt; width: 100%;"></div>
<strong class="printedlargetext">
<div id="_myo_OrderID">
    <?php echo ($order_id_word); ?>: <?php echo ($vo["order_number"]); ?>
</div>
</strong>
<p style="font-size: 20px;"><?php echo ($thank_word); ?></p>
<p style="font-size: 30px;">
<?php if(is_array($vo['factory_order'])): $i = 0; $__LIST__ = $vo['factory_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$factory_order_vo): $mod = ($i % 2 );++$i; if($factory_order_vo['order_platform_id'] != 0) { $order_fac_type = 'plat'; } elseif($factory_order_vo['order_id'] !=0) { $order_fac_type = 'web'; } ?>
<?php echo get_factory_str($factory_order_vo['factory_id'],$factory_order_vo['date'],$factory_order_vo['number'],'execl',$order_fac_type);?>
&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>

<?php if(is_array($vo['fba_order'])): $i = 0; $__LIST__ = $vo['fba_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fba_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['orderplatform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
 FBA<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($fba_order_vo["date"]))); ?>-<?php if($fba_order_vo["number"] < 10): ?>0<?php endif; echo ($fba_order_vo["number"]); ?>
 &nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>

<?php if(is_array($vo['product_stock_order'])): $i = 0; $__LIST__ = $vo['product_stock_order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_stock_order_vo): $mod = ($i % 2 );++$i; if($fba_order_vo['order_platform_id'] != 0) { $order_fac_type_bz = 'W'; } elseif($fba_order_vo['order_id'] !=0) { $order_fac_type_bz = ''; } ?>
K<?php echo ($order_fac_type_bz); echo (date('m.d',strtotime($product_stock_order_vo["date"]))); ?>-<?php if($product_stock_order_vo["number"] < 10): ?>0<?php endif; echo ($product_stock_order_vo["number"]); ?>
&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
    </p>            
<div class="bucketcontainer" style="margin:20pt 0; border: 1px solid #000000">
  <table border="0" cellpadding="0" cellspacing="0" style="padding: 5px;" width="100%">
   <tbody><tr>
      <td width="50%" class="printednormaltext"><strong><?php echo ($shipping_adress_word); ?>:</strong><br>
      <?php echo ($vo['shipping_info']['name']); ?><br>
                <?php echo ($vo['shipping_info']['address1']); ?>&nbsp;<?php echo ($vo['shipping_info']['address2']); ?>&nbsp;<?php echo ($vo['shipping_info']['address3']); ?><br>	
                <?php echo ($vo['shipping_info']['city']); ?><br>	
                <?php echo ($vo['shipping_info']['state']); ?><br>	
                <?php echo ($vo['shipping_info']['country']); ?><br>	
                <?php echo ($vo['shipping_info']['post']); ?><br>
      </td>
      <td align="right"><table cellpadding="0" cellspacing="0" width="100%">
        <tbody>
        <tr>
          <td align="left" nowrap="nowrap" width="10%" class="printednormaltext"><?php echo ($order_date_word); ?>:</td>
          <td width="20"></td>
          <td align="left" nowrap="nowrap" class="printednormaltext"><?php echo date("Y-M-d",strtotime($vo[date_time]));?></td>
        </tr>
        <tr>
          <td align="left" nowrap="nowrap" width="10%" class="printednormaltext"><?php echo ($shipping_service_word); ?>:</td>
          <td>&nbsp;</td>
          <td align="left" nowrap="nowrap" class="printednormaltext"><?php echo ($vo[ship_service_level]); ?></td>
        </tr>
        <tr>
          <td align="left" nowrap="nowrap" width="10%" class="printednormaltext"><?php echo ($buyer_name_word); ?>:</td>
          <td>&nbsp;</td>
          <td align="left" nowrap="nowrap" class="printednormaltext"><?php echo ($vo['shipping_info']['name']); ?></td>
        </tr>
        <tr>
          <td align="left" nowrap="nowrap" width="10%" class="printednormaltext"><?php echo ($seller_name_word); ?>:</td>
          <td>&nbsp;</td>
          <td align="left" nowrap="nowrap" class="printednormaltext">LILYSILK</td>
        </tr>
      </tbody></table></td>
    </tr>
  </tbody></table>
</div>
<div class="printbucket">
  <table class="printedtable" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr>
    <th class="leftcolumn" width="10%"><?php echo ($quantity_word); ?></th>
    <th width="55%" align="left"><?php echo ($product_details_word); ?></th>
    <th width="10%"><?php echo ($price_word); ?></th>
    <th width="25%"><?php echo ($total_word); ?></th>
    </tr>
     <?php if(is_array($vo['product_info'])): $i = 0; $__LIST__ = $vo['product_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_list): $mod = ($i % 2 );++$i; if($product_list[status]=="cancel") { continue; } ?>
    <tr class="shippeditem">
    <td align="center" width="10%" class="printednormaltext leftcolumn"><?php echo ($product_list["number"]); ?></td>
    <td width="55%" class="printednormaltext">
    <strong><?php echo ($product_list["name"]); ?></strong>
    <div class="printedsubtext" style="margin-top: 10px;">
    <?php if(!empty($product_list[sku])): ?><strong><?php echo ($sku_word); ?>:</strong> <?php echo ($product_list["sku"]); ?><br><?php endif; ?>
    <?php if(!empty($product_list[extra_amazon_info][asin])): ?><strong><?php echo ($asin_word); ?>:</strong> <?php echo ($product_list["extra_amazon_info"]["asin"]); ?><br><?php endif; ?>
     <?php if(!empty($product_list[extra_amazon_info][orderitemid])): ?><strong><?php echo ($order_item_id_word); ?>:</strong> <?php echo ($product_list["extra_amazon_info"]["orderitemid"]); ?><br><?php endif; ?>
    <?php if(!empty($product_list[extra_amazon_info][conditionid])): ?><strong><?php echo ($condition_word); ?>:</strong> <?php echo ($product_list["extra_amazon_info"]["conditionid"]); ?><br><?php endif; ?>
    </div>
    </td>
    <td align="center" width="10%" class="printednormaltext" nowrap=""><?php echo ($product_list["extra_amazon_info"]["currencycode"]); ?> <?php echo ($product_list["extra_amazon_info"]["itemprice"]); ?></td>
    <td align="center" width="25%" class="printednormaltext">
		<table width="100%" border="0" cellspacing="0" cellpadding="1" class="printedtotal"><tbody>
		  <tr>
		    <td class="smaller" align="right" width="60%"><?php echo ($subtotal_word); ?>:</td>
		    <td class="smaller" align="right" nowrap=""><?php echo ($product_list["extra_amazon_info"]["currencycode"]); ?> <?php echo ($product_list["extra_amazon_info"]["itemprice"]); ?></td>
		  </tr>
		  <?php if($product_list[extra_amazon_info][shippingprice] > 0): ?><tr>
		    <td class="smaller" align="right" width="60%">Shipping:</td>
		    <td class="smaller" align="right" width="40%" nowrap=""><?php echo ($product_list["extra_amazon_info"]["currencycode"]); ?> <?php echo ($product_list["extra_amazon_info"]["shippingprice"]); ?></td>
		  </tr><?php endif; ?>
		  <?php if($product_list[extra_amazon_info][promotiondiscount] > 0): ?><tr>
		    <td class="smaller" align="right" width="60%"><?php echo ($promotions_word); ?>:</td>
		    <td class="smaller" align="right" width="40%" nowrap="">(<?php echo ($product_list["extra_amazon_info"]["currencycode"]); ?> <?php echo ($product_list["extra_amazon_info"]["promotiondiscount"]); ?>)</td>
		  </tr><?php endif; ?>
		  <tr>
		    <td colspan="2"><hr color="#cccccc" noshade="noshade" size="1"></td>
		  </tr>
		  <tr>
		    <td class="small" align="right"><?php echo ($total_td_word); ?>:</td>
		    <td class="small" align="right" nowrap="">
			      <?php echo ($product_list["extra_amazon_info"]["currencycode"]); ?> <?php echo ($product_list[extra_amazon_info][itemprice]+$product_list[extra_amazon_info][shippingprice]-$product_list[extra_amazon_info][promotiondiscount]); ?>
		    </td>
		  </tr>
		  <tr>
		    <td colspan="2"><hr color="#cccccc" noshade="noshade" size="1"></td>
		  </tr>
		</tbody></table>
    </td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>

    <tr class="shippeditem">
      <td colspan="4/8" align="right" class="printednormaltext">
        <strong>
          <?php echo ($order_total_word); ?>: &nbsp;<?php echo ($vo[currency]); ?> <?php echo ($vo[price]); ?>
        </strong>
      </td>
    </tr>

  </tbody></table>
</div>


<br>
<div class="printbucket printednormaltext">
    <?php echo ($thanks_line); ?>
</div>




<br>
<br>

</div>

<p  <?php if($info_num > $k): ?>class="fenye"<?php endif; ?> style="text-align:right;font-size:25px;width:1200px;margin:auto;">&nbsp;</p>

<?php } } endforeach; endif; else: echo "" ;endif; ?>
<script language= "JavaScript">
window.onload=function(){
	 window.print();
}
</script>
</body>
</html>