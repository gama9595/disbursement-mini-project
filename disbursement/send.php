<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/api.php';
include_once '../config/db.php';
include_once '../objects/disburse.php';

$database = new dbconnection();

$db = $database->connect();

$disburse = new Disburse($db);

$curl = curl_init($base_url);

$bank_code = $_POST['bank_code'];
$account_number = $_POST['account_number'];
$amount = $_POST['amount'];
$remark = $_POST['remark'];

curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $secret_key);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, "bank_code=" . $bank_code . "&account_number=" . $account_number . "&amount=" . $amount . "&remark=" . $remark . "");
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$curl_response = curl_exec($curl);

$response = json_decode($curl_response, true);

curl_close($curl);


$disburse->id                   = $response['id'];
$disburse->amount               = $response['amount'];
$disburse->status               = $response['status'];
$disburse->timestamp            = $response['timestamp'];
$disburse->bank_code            = $response['bank_code'];
$disburse->account_number       = $response['account_number'];
$disburse->beneficiary_name     = $response['beneficiary_name'];
$disburse->remark               = $response['remark'];
$disburse->receipt              = $response['receipt'];
$disburse->time_served          = $response['time_served'];
$disburse->fee                  = $response['fee'];

if ($disburse->send_disbursement()) {
    echo json_encode(
        array("status" => "200", "message" => "SUCCESSFULLY SENT DISBURSEMENT DATA.")
    );
} else {
    echo json_encode(
        array("status" => "404", "message" => "OOPS, AN ERROR OCCURRED WITH THE SYSTEM.")
    );
}
