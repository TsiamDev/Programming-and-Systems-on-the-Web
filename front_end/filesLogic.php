<?php
// connect to the database
//$conn = mysqli_connect('localhost', 'root', '', 'user_config');
$signals = array();
$warns = array();
$har='';

require 'config/db.php';


// Uploads files
if (isset($_POST['save'])) { // if save button on the form is clicked
    // name of the uploaded file
    $filename = $_FILES['myfile']['name'];

    // destination of the file on the server
    $destination = 'uploads/' . $filename;

    // get the file extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['myfile']['tmp_name'];
    $size = $_FILES['myfile']['size'];  
    $sql = 'SELECT id FROM files INNER JOIN users ON files.id=users.id';
    if (!in_array($extension, ['pdf', 'har'])) {
        $signals['har'] = "H επέκταση του αρχείου πρέπει να είναι .har";
    } elseif ($_FILES['myfile']['size'] > 10000000) { 
        $signals['har'] = "Το μέγεθος του αρχείο είναι πολύ μεγάλο.";
    } else {
        // move the uploaded (temporary) file to the specified destination
        if (move_uploaded_file($file, $destination)) {
            $sql = "INSERT INTO files ( name, size, downloads) VALUES ( '$filename', $size, 0)";
            if (mysqli_query($conn, $sql)) {
                $warns['har']='Tο upload του αρχείου ολοκληρώθηκε.';
            }
        } else {
            $signals['har'] = "To upload του αρχείου απέτυχε.";
        }
    }
}