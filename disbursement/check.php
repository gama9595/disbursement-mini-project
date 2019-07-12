<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/api.php';
include_once '../config/db.php';
include_once '../objects/disburse.php';

$database = new dbconnection();

$db = $database->connect();

$disburse = new Disburse($db);



//CHECK THE DISBURSEMENT STATUS BASED ID TRANSACTION
$id = $_GET['id'];

$curl = curl_init($base_url . '/' . $id);

curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $secret_key);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$curl_response = curl_exec($curl);

$response = json_decode($curl_response, true);

curl_close($curl);



// UPDATE THE INFORMATION FROM DISBURSEMENT STATUS ENDPOINT TO DATABASE BASED TRANSACTION ID
$disburse->id                   = $id;
$disburse->status               = $response['status'];
$disburse->receipt              = $response['receipt'];
$disburse->time_served          = $response['time_served'];

if ($disburse->check_disbursement()) {
    echo json_encode(
        array("status" => "200", "message" => "SUKSES MEMPERBAHARUI STATUS DAN MENYIMPAN DATA TRANSAKSI.", "data" => $response)
    );
} else {
    echo json_encode(
        array("status" => "404", "message" => "OOPS, TELAH TERJADI KESALAHAN PADA SISTEM.")
    );
}
