<?php
header("Content-Type:application/json");

if (isset($_GET['network']) && isset($_GET['mobile']) && isset($_GET['network_code']) && isset($_GET['apiToken']) && isset($_GET['ref'])  && $_GET['network']!="" && $_GET['mobile']!="" && $_GET['network_code']!="" && $_GET['apiToken']!="" && $_GET['ref']!=""){

$network_code=$_GET['network_code'];


if ($network_code!=500 && $network_code!=1000 && $network_code!=2000 && $network_code!=3000 && $network_code!=5000 && $network_code!=10000 && $network_code!=15000 && $network_code!=20000)
{

$response=array(
    'code'=>'700', //Invalid Network Code
    'desc'=>'Invalid Network Code',
  );
  echo json_encode($response);

}

else{

//include("../system/config.php");
//require("mtnprice.php");

$network_code=$_GET['network_code'];
$mobile=$_GET['mobile'];
$network=$_GET['network'];
$apiToken=$_GET['apiToken'];
$ref=$_GET['ref'];

$verifyapi=mysqli_query($db, "SELECT * FROM users WHERE token='$apiToken'");
if (mysqli_num_rows($verifyapi)<1){

$response=array(
		'code'=>'300', ////Record Not Found
		'desc'=>'Record Not Found',
	);
	echo json_encode($response);
	exit();


}
else {

    $apidata=mysqli_fetch_array($verifyapi);
	$balance=$apidata['ng_wallets'];
	$apiToken=$apidata['token'];
	$username=$apidata['username'];
	$email=$apidata['email'];
	$mode=$apidata['x1'];
	$api_level=$apidata['api_level'];
	
	require("mtnprice.php");
	
if ($mode==1){


	if ($network_code==500){

    $server="500MB";
    $NETORKID=1;
    $plan_id=6;
    $amount=$mtn500mb;
}

   if ($network_code==1000){

    $server="1GB";
    $NETORKID=1;
    $plan_id=7;
    $amount=$mtn1gb;

}

   if ($network_code==2000){

   $server="2GB";
   $NETORKID=1;
   $plan_id=8;
   $amount=$mtn2gb;

}

   if ($network_code==3000){

   $server="3GB";
   $NETORKID=1;
   $plan_id=44;
   $amount=$mtn3gb;

}

   if ($network_code==5000){

   $server="5GB";
   $NETORKID=1;
    $plan_id=11;
   $amount=$mtn5gb;

}

   if ($network_code==10000){

   $server="10GB";
   $NETORKID=1;
   $plan_id=43;
   $amount=$mtn10gb;

}


////////////////////////////////////////

if ($balance<$amount){

$response=array(
		'code'=>'800', ////API BALANCE LOW
		'desc'=>'API BALANCE LOW',
	);
	echo json_encode($response);
}

else{

$dateTime = new DateTime('now', new DateTimeZone('Africa/Lagos')); 
$time=$dateTime->format("d-M-y  h:i A");


$newbal = $balance-$amount;
mysqli_query($db,"UPDATE users SET ng_wallets='".$newbal."' WHERE token='".$apiToken."'");
 


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://thechosendata.com/api/data/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_TIMEOUT_MS =>100000,
   CURLOPT_POSTFIELDS => \json_encode(array(
       'network'=>1,
       'mobile_number'=>$mobile,
       'plan'=>$plan_id,
       'Ported_number'=>true)),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Token b8e456103892a9f6d4d8145710718bda7caf57ad',
    'Content-Type: application/json'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
//echo $response;
$xx=json_decode($response, true);

$status=$xx['Status'];
$descr=$xx['api_response'];


// if ($status=="successful"){
 if ($response){

    


// $descr=$network.' '.$server.' Purchase To '.$mobile;

   mysqli_query($db,"INSERT INTO `mytransaction` (`id`, `email`, `username`, `amount`, `descr`, `status`, `date`, `active`, `trx`,`oldbal`,`newbal`) VALUES (NULL, '".$email."', '".$username."', '".$amount."', '".$descr."', 'Successful', '".$time."', 'API', '".$ref."', '".$balance."','".$newbal."')");

   $response=array(
		'code'=>'200', ////Record Found
		'desc'=>'success',
		'api_response'=>$descr,
	);
	echo json_encode($response);
	exit();
 }

 else{

 $newbal2=$balance;
 mysqli_query($db,"UPDATE users SET ng_wallets='".$newbal2."' WHERE token='".$apiToken."'");

 $descr='Unsuccessful '.$network.' '.$server.' to '.$mobile;

 mysqli_query($db,"INSERT INTO `mytransaction` (`id`, `email`, `username`, `amount`, `descr`, `status`, `date`, `active`, `trx`,`oldbal`,`newbal`) VALUES (NULL, '".$email."', '".$username."', '".$amount."', '".$descr."', 'Unsuccessful', '".$time."', 'API', '".$ref."', '".$balance."','".$newbal2."')");

$response=array(
		'code'=>'900', ////Record Not Found
		'desc'=>'Transaction Unsuccessful',
	);
	echo json_encode($response);
	exit();

}

}
}

else{
    
 	$response=array(
		'code'=>'500',
		'desc'=>'You are not allowed to access this services',
	);
	echo json_encode($response);  
	exit();
}
}

}

}
else {

	$response=array(
		'code'=>'500',
		'desc'=>'Imcomplete Parameter',
	);
	echo json_encode($response);

}





?>
