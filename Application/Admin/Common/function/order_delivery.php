<?php 
function delivery($order_id,$order_platform_id)
{
    $delivery_parameters=M('order_delivery_parameters');
    $delivery_parameters_detail=$delivery_parameters->where("order_id=$order_id and order_platform_id=$order_platform_id")->select();
    if($delivery_parameters_detail)
    {
        $weight=$delivery_parameters_detail[0]["weight"];
        $hs_bm=$delivery_parameters_detail[0]["hs"];
        $hs_name=$delivery_parameters_detail[0]["name"];
        $hs_price=$delivery_parameters_detail[0]["price"];
        if($order_id!=0)
        {
            $order_web_address=M("order_web_address");
            $order_web_address_detail=$order_web_address->where("order_web_id=$order_id")->select();
            $name=str_rewrite($order_web_address_detail[0]["first_name"]." ".$order_web_address_detail[0]["last_name"]);
            $country=$order_web_address_detail[0]["country"];
            $province=$order_web_address_detail[0]["province"];
            $city=str_rewrite($order_web_address_detail[0]["city"]);
            $code=$order_web_address_detail[0]["code"];
            $address=str_rewrite($order_web_address_detail[0]["address"]);
			$address=str_replace("<br>","",$address);
            $address1 = substr($address,0,30);
            $address2 = substr($address,30,30);
            $address3 = substr($address,60,30);
            $telephone=$order_web_address_detail[0]["telephone"];
            $order_number=$order_web_address_detail[0]["id"];
        }
        else
        {
            $order_plat_form_shipping=M("order_plat_form_shipping");
            $order_plat_form_shipping_detail=$order_plat_form_shipping->where("order_platform_id=$order_platform_id")->select();
            $name=str_rewrite($order_plat_form_shipping_detail[0]["name"]);
            $country=$order_plat_form_shipping_detail[0]["country"];
            $province=$order_plat_form_shipping_detail[0]["state"];
            $city=str_rewrite($order_plat_form_shipping_detail[0]["city"]);
            $code=$order_plat_form_shipping_detail[0]["post"];
            $address1=str_rewrite($order_plat_form_shipping_detail[0]["address1"]);
            $address2=str_rewrite($order_plat_form_shipping_detail[0]["address2"]);
            $address3=str_rewrite($order_plat_form_shipping_detail[0]["address3"]);
            $telephone=$order_plat_form_shipping_detail[0]["telephone"];
            $order_number=$order_plat_form_shipping_detail[0]["order_platform_id"];
        }
        $code_array=explode("-",$code);
        $code=$code_array[0];
        $StreetLines=$address1." ".$address2." ".$address3;
        $order_detail=array("name"=>$name,"streetline"=>$StreetLines,"city"=>$city,"state"=>$province,"country"=>$country,"phone"=>$telephone,"code"=>$code,"hs_bm"=>$hs_bm,"hs_name"=>$hs_name,"hs_price"=>$hs_price,"weight"=>$weight,"date_time"=>date('dMY'));
        if($delivery_parameters_detail[0]["shipping_style"]=="DHL")
        {
            $XML_day=date("Y-m-d",time());
            $XML_time=date("00".":"."00".":00.000-08:00");
            $XML_date_time=$XML_day."T".$XML_time;
            $hs_price=number_format($hs_price,'2');
            $XML="<ns1:ShipmentValidateRequestAP  xmlns:ns1='http://www.dhl.com' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'><Request><ServiceHeader><MessageTime>$XML_date_time</MessageTime><MessageReference>1234567890123460000000000000000</MessageReference><SiteID>MONOXML</SiteID><Password>0zkFC9BNm2</Password></ServiceHeader></Request><LanguageCode>en</LanguageCode><PiecesEnabled>Y</PiecesEnabled><Billing><ShipperAccountNumber>602346445</ShipperAccountNumber><ShippingPaymentType>S</ShippingPaymentType><DutyPaymentType>S</DutyPaymentType></Billing>";
$XML.="<Consignee><CompanyName>".$name."</CompanyName><AddressLine>$address1</AddressLine><AddressLine>$address2</AddressLine><AddressLine>$address3</AddressLine><City>".$city."</City>";
	       if($address_detail[0][country]=="US")
	       {
		      $XML.="<DivisionCode>".$province."</DivisionCode>";
	       }
	       $XML.="<PostalCode>".$code."</PostalCode><CountryCode>".$country."</CountryCode><CountryName>".$country."</CountryName><Contact><PersonName>".$name."</PersonName><PhoneNumber>".$telephone."</PhoneNumber></Contact></Consignee>";  $XML.="<Commodity><CommodityCode>$hs_bm</CommodityCode><CommodityName>String</CommodityName></Commodity><Dutiable><DeclaredValue>".$hs_price."</DeclaredValue><DeclaredCurrency>USD</DeclaredCurrency><ShipperEIN>Text</ShipperEIN><TermsOfTrade>DTP</TermsOfTrade></Dutiable><Reference><ReferenceID>$order_number</ReferenceID><ReferenceType>St</ReferenceType></Reference><ShipmentDetails><NumberOfPieces>1</NumberOfPieces><CurrencyCode>CNY</CurrencyCode><Pieces><Piece><Weight>0.5</Weight><Depth>1</Depth><Width>1</Width><Height>1</Height></Piece></Pieces><PackageType>OD</PackageType><Weight>$weight</Weight><DimensionUnit>C</DimensionUnit><WeightUnit>K</WeightUnit><GlobalProductCode>P</GlobalProductCode><LocalProductCode>P</LocalProductCode><DoorTo>DD</DoorTo><Date>$XML_day</Date><Contents>$hs_name</Contents><InsuredAmount>0.00</InsuredAmount></ShipmentDetails><Shipper><ShipperID>602346445</ShipperID>";
	       if($country=="jp")
	       {
	           $XML.="<CompanyName>LilySilk Bedding Manufacture,L</CompanyName><AddressLine>(PLDW)(N3P)RM 201 UNIT 1 BLDG</AddressLine><AddressLine>RM 201,UNIT 1,BLDG 2</AddressLine><City>Nanjin</City><PostalCode>211100</PostalCode><CountryCode>CN</CountryCode><CountryName>China</CountryName><Contact><PersonName>DAVID WONG</PersonName><PhoneNumber>862552179794</PhoneNumber></Contact></Shipper><LabelImageFormat>EPL2</LabelImageFormat></ns1:ShipmentValidateRequestAP>";
	       }
	       else
	       {
	           $XML.="<CompanyName>MONO INTERNET TECHNOLOGY LTD</CompanyName><AddressLine>373 Zhushan Road</AddressLine><AddressLine>373 Zhushan Road</AddressLine><City>Nanjin</City><PostalCode>211100</PostalCode><CountryCode>CN</CountryCode><CountryName>China</CountryName><Contact><PersonName>Xiangyuan Jin</PersonName><PhoneNumber>862552179794</PhoneNumber></Contact></Shipper><LabelImageFormat>EPL2</LabelImageFormat></ns1:ShipmentValidateRequestAP>";
	       }
	       $URL="http://xmlpi-ea.dhl.com/XMLShippingServlet";
	       $ch = curl_init();
	       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	       curl_setopt($ch, CURLOPT_URL, $URL);
	       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	       curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	       curl_setopt($ch, CURLOPT_POSTFIELDS, $XML);
	       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
	       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	       $output = curl_exec($ch);
	       $obj=simplexml_load_string($output);
	       $array=get_object_vars($obj);
	       $ActionNote=$array[Note]->ActionNote;
	       if($ActionNote=="Success")
	       {
	           $OutputImage=$ActionNote=$array[LabelImage]->OutputImage;
	           $order_detail["trackingNumber_str"]=$array[AirwayBillNumber];
	           $Temp = base64_decode($OutputImage);
	           $Temp = nl2br($Temp);
	           $Temp_array=explode("<br />",$Temp);
	           $date_time=date('y-m-d H:i:s',time());
			   $order_delivery_detail = M('order_delivery_detail');
               $detail_data['order_web_id'] = $order_id;
               $detail_data['order_platform_id'] = $order_platform_id;
               $detail_data['style'] = "DHL";
               $detail_data['delivery_number'] = $order_detail["trackingNumber_str"];
               $detail_data['message'] = "normal";
               $detail_data['status'] = "normal";
               $detail_data['time'] = $date_time;
               $order_delivery_detail->add($detail_data);

	           if($order_platform_id!=0)
	           {
	               $order_plat_form_status=M("order_plat_form_status");
	               $status_date["status"]="history";
	               $order_plat_form_status->where("order_platform_id=$order_platform_id")->save($status_date);
	           
	               $order_plat_form_status_history=M("order_plat_form_status_history");
	               $status_history_date["order_platform_id"]=$order_platform_id;
	               $status_history_date["status"]="shipped";
	               $status_history_date["message"]="";
	               $status_history_date["date_time"]=time();
	               $status_history_date["operator"]=session('username');
	               $order_plat_form_status_history->add($status_history_date);
	           }
	           else
	           {
	               $order_web_status=M("order_web_status");
	               $status_date["status"]="history";
	               $order_web_status->where("order_web_id=$order_id")->save($status_date);
	           
	               $order_web_status_history=M("order_web_status_history");
	               $status_history_date["order_web_id"]=$order_id;
	               $status_history_date["status"]="shipped";
	               $status_history_date["message"]="";
	               $status_history_date["date_time"]=time();
	               $status_history_date["operator"]=session('username');
	               $order_web_status_history->add($status_history_date);
	           }
	           
	           
?>
	           <script>
	           var fso, tf;
	           fso = new ActiveXObject("Scripting.FileSystemObject");
	           tf = fso.CreateTextFile("D:\\dhl\\epl2.txt", true);
	           </script>
<?php
	           foreach($Temp_array as $key=>$value)
	           {
	               $value=str_replace('"', '\"', $value);
				    if(strpos($value,"GW0"))
					{
						continue;
					}
?>
	           <script>
	           tf.WriteLine("<?php echo trim($value);?>");
	           </script>
<?php 
	           }
?>
	           <script>
	           tf.Close();
	           WSH = new ActiveXObject("WScript.Shell");
	           WSH.Run("cmd.exe /k " + "D:\\dhl\\xml.cmd",1,true);
	           </script>
<?php
                
			   if($order_id!=0)
			   {
					$order_web=M("order_web");
					$order_web_detail=$order_web->where("id=$order_id")->select();
					send_email_function($order_web_detail[0]["email"],$name,$order_web_detail[0]["order_number"],"DHL",$order_detail["trackingNumber_str"],$country);
			   }
			   return $order_detail;
	       }
	       else
	       {
	           return false;
	       }
	       
        }
        elseif($delivery_parameters_detail[0]["shipping_style"]=="FEDEX")
        {
            $packing='FEDEX_PAK';
            if($weight>3.5)
            {
                $packing="YOUR_PACKAGING";
            }
            require_once('fedexcommon/fedex-common.php');
            //The WSDL is not included with the sample code.
            //Please include and reference in $path_to_wsdl variable.
            $path_to_wsdl = "http://www.lilysilk.com/mono_admin/mono_admin_template/fedexcommon/ShipService_v15.wsdl";
            define('SHIP_LABEL', 'shipexpresslabel.pdf');  // PDF label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
            //ini_set("soap.wsdl_cache_enabled", "0");
            $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
            $request['WebAuthenticationDetail'] = array(
            'UserCredential' =>array(
                'Key' => getProperty('key'),
                'Password' => getProperty('password')
            )
            );
            $request['ClientDetail'] = array(
                'AccountNumber' => getProperty('shipaccount'),
                'MeterNumber' => getProperty('meter')
            );
            $request['TransactionDetail'] = array('CustomerTransactionId' => '*** Express International Shipping Request using PHP ***');
            $request['Version'] = array(
                'ServiceId' => 'ship',
                'Major' => '15',
                'Intermediate' => '0',
                'Minor' => '0'
            );
            $request['RequestedShipment'] = array(
                'ShipTimestamp' => date('c'),
                'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
                'ServiceType' => 'INTERNATIONAL_PRIORITY', // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
                'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
                'Shipper' => array(
                    'Contact' => array(
                        'PersonName' => 'Jia Lv',
                        'CompanyName' => 'nanjin mono',
                        'PhoneNumber' => '15850574562'
                    ),
                    'Address' => array(
                        'StreetLines' => array('373 liayinda road jiangning district'),
                        'City' => 'nanjin',
                        'StateOrProvinceCode' => 'jiangsu',
                        'PostalCode' => '211100',
                        'CountryCode' => 'CN'
                    )
                ),
                'Recipient' => array(
                    'Contact' => array(
                        'PersonName' => $name,
                        'CompanyName' => $name,
                        'PhoneNumber' => $telephone
                    ),
                    'Address' => array(
                        'StreetLines' => array($StreetLines),
                        'City' => $city,
                        'StateOrProvinceCode' => $province,
                        'PostalCode' => $code,
                        'CountryCode' => $country,
                        'Residential' => false
                    )
                ),
                'ShippingChargesPayment' =>  array('PaymentType' => 'SENDER',
                    'Payor' => array(
                        'ResponsibleParty' => array(
                            'AccountNumber' => getProperty('billaccount'),
                            'Contact' => null,
                            'Address' => array(
                                'CountryCode' => 'cn'
                            )
                        )
                    )
                ),
                'CustomsClearanceDetail' => array(
                    'DutiesPayment' => array(
                        'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
                        'Payor' => array(
                            'ResponsibleParty' => array(
                                'AccountNumber' => getProperty('dutyaccount'),
                                'Contact' => null,
                                'Address' => array(
                                    'CountryCode' => 'CN'
                                )
                            )
                        )
                    ),
                    'DocumentContent' => 'NON_DOCUMENTS',
                    'CustomsValue' => array(
                        'Currency' => 'USD',
                        'Amount' => $hs_price
                    ),
                    'Commodities' => array(
                        '0' => array(
                            'NumberOfPieces' => 1,
                            'Description' => $hs_name,
                            'CountryOfManufacture' => 'cn',
                            'Weight' => array(
                                'Units' => 'KG',
                                'Value' => $weight
                            ),
                            'Quantity' => 1,
                            'QuantityUnits' => 'EA',
                            'UnitPrice' => array(
                                'Currency' => 'USD',
                                'Amount' => $hs_price
                            ),
                            'CustomsValue' => array(
                                'Currency' => 'USD',
                                'Amount' => $hs_price
                            )
                        )
                    ),
                    'ExportDetail' => array(
                        'B13AFilingOption' => 'NOT_REQUIRED'
                    )
                ),
                'LabelSpecification' => array(
                    'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
                    'ImageType' => 'EPL2',  // valid values DPL, EPL2, PDF, ZPLII and PNG
                    'LabelStockType' => 'STOCK_4X6.75_LEADING_DOC_TAB'
                ),
                'CustomerSpecifiedDetail' => array(
                    'MaskedData'=> 'SHIPPER_ACCOUNT_NUMBER'
                ),
                'PackageCount' => 1,
                'RequestedPackageLineItems' => array(
                    '0' =>array(
                        'SequenceNumber'=>1,
                        'GroupPackageCount'=>1,
                        'Weight' => array(
                            'Value' => $weight,
                            'Units' => 'KG'
                        )
                    )
                ),
                'CustomerReferences' => array(
                    '0' => array(
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => 'TC007_07_PT1_ST01_PK01_SNDUS_RCPCA_POS'
                    )
                )
            );
            try{
                if(setEndpoint('changeEndpoint')){
                    $newLocation = $client->__setLocation(setEndpoint('endpoint'));
                }
                $response = $client->processShipment($request); // FedEx web service invocation
                if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){
                    $success_xml=printSuccess($client, $response);
                    preg_match_all('/\<TrackingNumber\>(.*?)\<\/TrackingNumber\>/', $success_xml,$trackingNumber);
                    preg_match_all('/\<Image\>(.*?)\<\/Image\>/', $success_xml,$epl2);
                    $Code=$epl2[1][0];
                    $trackingNumber_str=$trackingNumber[1][0];
                    $Temp = base64_decode($Code);
                    $Temp = str_replace("'","\'", $Temp);
                    $Temp = nl2br($Temp);
                    $Temp_array=explode("<br />",$Temp);
                    $date_time=date('y-m-d H:i:s',time());
                    $order_detail["trackingNumber_str"]=$trackingNumber_str;
                    $order_delivery_detail = M('order_delivery_detail');
                    $detail_data['order_web_id'] = $order_id;
                    $detail_data['order_platform_id'] = $order_platform_id;
                    $detail_data['style'] = "Fedex";
                    $detail_data['delivery_number'] = $trackingNumber_str;
                    $detail_data['message'] = "normal";
                    $detail_data['status'] = "normal";
                    $detail_data['time'] = $date_time;
                    $order_delivery_detail->add($detail_data);
                    if($order_platform_id!=0)
                    {
                        $order_plat_form_status=M("order_plat_form_status");
                        $status_date["status"]="history";
                        $order_plat_form_status->where("order_platform_id=$order_platform_id")->save($status_date);
                        
                        $order_plat_form_status_history=M("order_plat_form_status_history");
                        $status_history_date["order_platform_id"]=$order_platform_id;
                        $status_history_date["status"]="shipped";
                        $status_history_date["message"]="";
                        $status_history_date["date_time"]=time();
                        $status_history_date["operator"]=session('username');
                        $order_plat_form_status_history->add($status_history_date);
                    }
                    else
                    {
                        $order_web_status=M("order_web_status");
                        $status_date["status"]="history";
                        $order_web_status->where("order_web_id=$order_id")->save($status_date);
                        
                        $order_web_status_history=M("order_web_status_history");
                        $status_history_date["order_web_id"]=$order_id;
                        $status_history_date["status"]="shipped";
                        $status_history_date["message"]="";
                        $status_history_date["date_time"]=time();
                        $status_history_date["operator"]=session('username');
                        $order_web_status_history->add($status_history_date);
                    }
                }
                else
                {
                    return false;
                }
            
                // writeToLog($client);    // Write to log file
            } catch (SoapFault $exception) {
                //printFault($exception, $client);
            }
			
            ?>
<script>
var fso, tf;
fso = new ActiveXObject("Scripting.FileSystemObject");
tf = fso.CreateTextFile("D:\\epl2\\epl2.txt", true);
</script>
<?php
foreach($Temp_array as $key=>$value)
{
    if(trim($value)=="P3")
    {
        $value="P4";
    }
    $value=str_replace('"', '\"', $value);
?>   
<script>
tf.WriteLine("<?php echo trim($value);?>");
</script>
<?php    
}
?>
<script>
tf.Close();
WSH = new ActiveXObject("WScript.Shell");
WSH.Run("cmd.exe /k " + "D:\\epl2\\xml.cmd",1,true);
</script>
<?php
if($order_id!=0)
{
	$order_web=M("order_web");
	$order_web_detail=$order_web->where("id=$order_id")->select();
	send_email_function($order_web_detail[0]["email"],$name,$order_web_detail[0]["order_number"],"FEDEX",$trackingNumber_str,$country);
}
return $order_detail;
        }
        elseif($delivery_parameters_detail[0]["shipping_style"]=="TNT")
        {
            $XML_day=date("d/m/Y",time());
            $telephone_code=substr($telephone,0,5);
            $telephone_number=substr($telephone,5);
            $XML="<ESHIPPER><LOGIN><COMPANY>lilysilk</COMPANY><PASSWORD>cmd0147896
            </PASSWORD><APPID>EC</APPID><APPVERSION>2.2</APPVERSION></LOGIN><CONSIGNMENTBATCH><SENDER><COMPANYNAME>LILY MANUFACTURER</COMPANYNAME><STREETADDRESS1>HAIXIN LINING NO.201 YANHU RD</STREETADDRESS1><CITY>NANJING-JIANGNING</CITY><PROVINCE>JIANGSU</PROVINCE><POSTCODE>211100</POSTCODE><COUNTRY>CN</COUNTRY><ACCOUNT>3057324</ACCOUNT><CONTACTNAME>xiangyuan jin</CONTACTNAME><CONTACTDIALCODE>025</CONTACTDIALCODE><CONTACTTELEPHONE>81034970</CONTACTTELEPHONE><CONTACTEMAIL>lj@monocn.com</CONTACTEMAIL><COLLECTION><SHIPDATE>$XML_day</SHIPDATE><PREFCOLLECTTIME><FROM>0900</FROM><TO>1200</TO></PREFCOLLECTTIME><ALTCOLLECTTIME><FROM>1300</FROM><TO>1700</TO></ALTCOLLECTTIME><COLLINSTRUCTIONS>use rear gate</COLLINSTRUCTIONS></COLLECTION></SENDER><CONSIGNMENT><CONREF>$order_number</CONREF><DETAILS><RECEIVER><COMPANYNAME>$name</COMPANYNAME><STREETADDRESS1>$StreetLines</STREETADDRESS1><STREETADDRESS2/><STREETADDRESS3/><CITY>$city</CITY><PROVINCE>$province</PROVINCE><POSTCODE>$code</POSTCODE><COUNTRY>$country</COUNTRY><VAT/><CONTACTNAME>$name</CONTACTNAME><CONTACTDIALCODE>$telephone_code</CONTACTDIALCODE><CONTACTTELEPHONE>$telephone_number</CONTACTTELEPHONE><CONTACTEMAIL/></RECEIVER><CUSTOMERREF></CUSTOMERREF><CONTYPE>N</CONTYPE><PAYMENTIND>S</PAYMENTIND><ITEMS>1</ITEMS><TOTALWEIGHT>$weight</TOTALWEIGHT><TOTALVOLUME>0.001</TOTALVOLUME><CURRENCY>USD</CURRENCY><GOODSVALUE>$hs_price</GOODSVALUE><INSURANCECURRENCY/><SERVICE>15N</SERVICE><DESCRIPTION>$hs_name</DESCRIPTION><DELIVERYINST/><PACKAGE><ITEMS>1</ITEMS><DESCRIPTION>$hs_name</DESCRIPTION><LENGTH>0.1</LENGTH><HEIGHT>0.1</HEIGHT><WIDTH>0.1</WIDTH><WEIGHT>1</WEIGHT><ARTICLE><ITEMS>1</ITEMS><DESCRIPTION>$weight</DESCRIPTION><WEIGHT>$weight</WEIGHT><INVOICEVALUE>$hs_price</INVOICEVALUE><INVOICEDESC>$hs_name</INVOICEDESC><HTS>$hs_bm</HTS><COUNTRY>$country</COUNTRY><EMRN></EMRN></ARTICLE></PACKAGE></DETAILS></CONSIGNMENT></CONSIGNMENTBATCH><ACTIVITY><CREATE><CONREF>$order_number</CONREF></CREATE><RATE><CONREF>$order_number</CONREF></RATE><SHIP><CONREF>$order_number</CONREF></SHIP><PRINT><CONNOTE><CONREF>$order_number</CONREF></CONNOTE><LABEL><CONREF>$order_number</CONREF></LABEL><MANIFEST><CONREF>$order_number</CONREF></MANIFEST><INVOICE><CONREF>$order_number</CONREF></INVOICE></PRINT></ACTIVITY></ESHIPPER>";
            
            $XML="xml_in=".$XML;
            $URL="http://www.lilysilk.com/mono_admin/mono_admin_template/tnt_delivery_api.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $XML);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $output = curl_exec($ch);
			if($output!="wrong")
			{
			    $Temp = nl2br($output);
			    $Temp_array=explode("<br />",$Temp);
			    $trackingNumber_str=$Temp_array[2];
			    $order_detail["trackingNumber_str"]=trim($trackingNumber_str);

				$date_time=date('y-m-d H:i:s',time());
			    $order_delivery_detail = M('order_delivery_detail');
                $detail_data['order_web_id'] = $order_id;
                $detail_data['order_platform_id'] = $order_platform_id;
                $detail_data['style'] = "TNT";
                $detail_data['delivery_number'] = $order_detail["trackingNumber_str"];
                $detail_data['message'] = "normal";
                $detail_data['status'] = "normal";
                $detail_data['time'] = $date_time;
                $order_delivery_detail->add($detail_data);
			    
			    if($order_platform_id!=0)
			    {
			        $order_plat_form_status=M("order_plat_form_status");
			        $status_date["status"]="history";
			        $order_plat_form_status->where("order_platform_id=$order_platform_id")->save($status_date);
			    
			        $order_plat_form_status_history=M("order_plat_form_status_history");
			        $status_history_date["order_platform_id"]=$order_platform_id;
			        $status_history_date["status"]="shipped";
			        $status_history_date["message"]="";
			        $status_history_date["date_time"]=time();
			        $status_history_date["operator"]=session('username');
			        $order_plat_form_status_history->add($status_history_date);
			    }
			    else
			    {
			        $order_web_status=M("order_web_status");
			        $status_date["status"]="history";
			        $order_web_status->where("order_web_id=$order_id")->save($status_date);
			    
			        $order_web_status_history=M("order_web_status_history");
			        $status_history_date["order_web_id"]=$order_id;
			        $status_history_date["status"]="shipped";
			        $status_history_date["message"]="";
			        $status_history_date["date_time"]=time();
			        $status_history_date["operator"]=session('username');
			        $order_web_status_history->add($status_history_date);
			    }
			    
			    if($order_id!=0)
				{
					$order_web=M("order_web");
					$order_web_detail=$order_web->where("id=$order_id")->select();
					send_email_function($order_web_detail[0]["email"],$name,$order_web_detail[0]["order_number"],"TNT",$order_detail["trackingNumber_str"],$country);
				}
				return $order_detail;
			}
			else
			{
				return false;
			}
		
        }
    }
}


