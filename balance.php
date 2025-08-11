<?php
$conn = oci_connect("C##DDMS_BANK","7032","localhost/orcl");
$acc = $_POST['acc_no'];
$stmt = oci_parse($conn,
  "BEGIN :bal := get_balance(:acc); END;");
oci_bind_by_name($stmt, ":acc", $acc);
oci_bind_by_name($stmt, ":bal", $bal, 32);
oci_execute($stmt);
echo "<h2>Balance: ₹".number_format($bal,2)."</h2>";
?>
