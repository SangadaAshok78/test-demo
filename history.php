<?php
// 1. Connect to Oracle
$conn = oci_connect("C##DDMS_BANK", "7032", "localhost/orcl");
if (!$conn) {
    $e = oci_error();
    die("Connection failed: " . $e['message']);
}

// 2. Grab the posted account number
$acc = $_POST['acc_no'];

// 3. Prepare the PL/SQL block
$sql = "BEGIN get_transactions(:acc, :rc); END;";
$stmt = oci_parse($conn, $sql);

// 4. Bind the input
oci_bind_by_name($stmt, ":acc", $acc);

// 5. Create **one** cursor resource
$rc = oci_new_cursor($conn);

// 6. Bind the cursor to your statement
//    -1 is the default length, OCI_B_CURSOR tells Oracle it's a cursor
oci_bind_by_name($stmt, ":rc", $rc, -1, OCI_B_CURSOR);

// 7. Execute the PL/SQL block
oci_execute($stmt);

// 8. Execute the cursor itself to populate it
oci_execute($rc);

// 9. Render results in HTML
echo "<table class='table'><tr>
        <th>ID</th><th>Type</th><th>Amount</th><th>Date</th><th>Description</th>
      </tr>";

while ($row = oci_fetch_array($rc, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>";
    foreach ($row as $col) {
        echo "<td>" . htmlspecialchars($col, ENT_QUOTES) . "</td>";
    }
    echo "</tr>";
}

echo "</table>";

// 10. Clean up
oci_free_statement($stmt);
oci_free_statement($rc);
oci_close($conn);
?>
