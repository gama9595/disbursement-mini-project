<?php
class disburse
{
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function send_disbursement()
    {
        $query = "INSERT INTO disburse SET id=:id, amount=:amount, status=:status, timestamp=:timestamp, bank_code=:bank_code, account_number=:account_number, beneficiary_name=:beneficiary_name, remark=:remark, receipt=:receipt, time_served=:time_served, fee=:fee";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":timestamp", $this->timestamp);
        $stmt->bindParam(":bank_code", $this->bank_code);
        $stmt->bindParam(":account_number", $this->account_number);
        $stmt->bindParam(":beneficiary_name", $this->beneficiary_name);
        $stmt->bindParam(":remark", $this->remark);
        $stmt->bindParam(":receipt", $this->receipt);
        $stmt->bindParam(":time_served", $this->time_served);
        $stmt->bindParam(":fee", $this->fee);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function check_disbursement()
    {
        $id = $this->id;

        $query = "UPDATE disburse SET status=:status, receipt=:receipt, time_served=:time_served WHERE id=$id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":receipt", $this->receipt);
        $stmt->bindParam(":time_served", $this->time_served);

        $stmt->execute();

        $affectedRows = $stmt->rowCount();

        if ($affectedRows == 1) {
            return true;
        }
        return false;
    }
}
