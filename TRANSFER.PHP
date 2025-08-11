<?php
// 1. Connect to Oracle
$conn = oci_connect('C##DDMS_BANK', '7032', 'localhost/ORCL');
if (!$conn) {
    $e = oci_error();
    die('<div class="alert alert-danger">Connection failed: '
        . htmlspecialchars($e['message'], ENT_QUOTES) . '</div>');
}

// 2. Collect and sanitize input
$sender   = strtoupper(trim($_POST['sender_acc']));
$receiver = strtoupper(trim($_POST['receiver_acc']));
$amount   = floatval($_POST['amount']);
$status   = '';

// 3. Prepare the PL/SQL call
$sql = 'BEGIN transfer_funds(:sender, :receiver, :amount, :status); END;';
$stmt = oci_parse($conn, $sql);

// 4. Bind parameters
oci_bind_by_name($stmt, ':sender',   $sender);
oci_bind_by_name($stmt, ':receiver', $receiver);
oci_bind_by_name($stmt, ':amount',   $amount);
oci_bind_by_name($stmt, ':status',   $status, 200);

// 5. Execute
oci_execute($stmt);

// 6. Display the result
if (stripos($status, 'Success') !== false) {
    echo '<div class="alert alert-success">Transfer completed successfully.</div>';
} else {
    echo '<div class="alert alert-warning">Status: ' 
         . htmlspecialchars($status, ENT_QUOTES) 
         . '</div>';
}

// 7. Clean up
oci_free_statement($stmt);
oci_close($conn);
?>
