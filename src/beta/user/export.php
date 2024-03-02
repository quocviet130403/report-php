<?php
session_start();

// Check if 'data' is set in the POST request
if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
print_r($data);
die("f");
    // Create a basic CSV file
    $file = 'exported_data.csv';
    
    // Open the file for writing
    $fileHandle = fopen($file, 'w');

    // Write the header row
    $header = ["Question", "Description", "Category", "User/Responder", "Score"];
    fputcsv($fileHandle, $header, ',');

    // Write the data rows
    foreach ($data as $row) {
        fputcsv($fileHandle, $row, ',');
    }

    // Close the file
    fclose($fileHandle);

    // Provide a download link or send the file to the browser
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="exported_data.csv"');
    readfile($file);
    unlink($file); // Optionally delete the file after download
    exit();
} else {
    echo "No data received.";
}
?>
