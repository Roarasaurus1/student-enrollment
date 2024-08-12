<?php
// Session handling.
include 'session.php';

// Page Title
$title = 'Validation';

// Error messages.
$errors = [];

// Form Vars
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$form_group = $_POST['form_group'];
$unit1_selections = $_POST['unit1'];
$unit2_selections = $_POST['unit2'];



// DB connection.
define('DB_SERVER', 'localhost');
define('DB_STUDENTS', 'admin');
define('DB_PASSWORD', 'Cisco99');
define('DB_DATABASE', 'school');
$conn = new mysqli(DB_SERVER,DB_STUDENTS,DB_PASSWORD,DB_DATABASE);



// Exit if student is already enrolled.
$stmt = $conn->prepare('SELECT COUNT(unit_uid)>0 AS is_enrolled FROM Students NATURAL JOIN Enrollments WHERE student_uid = ?');
// or use db view instead for simpler query: 'SELECT is_enrolled FROM StudentEnrolments WHERE student_uid = ?'
$stmt->bind_param('s', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($is_enrolled);
$stmt->fetch();
$stmt->close();

if ($is_enrolled == 1) {
        echo '<script>
                alert("You are already enrolled.");
                location.href="home.php";
        </script>';
        exit;
}




///////////////////////////////////////
// START | Validate Personal Details //

// Validate Date of Birth (example: must be between 2000-01-01 and today)
$min_date = '2000-01-01';
$max_date = date('Y-m-d');
if ($dob < $min_date || $dob > $max_date) {
        array_push($errors, "Date of Birth must be between $min_date and $max_date.");
}



// Validate form group.
$valid_form_groups = array("10A","10B","10C","10D","10E","10F","10G");

function in_arrayi($value, $array) {
        return in_array(strtolower($value), array_map('strtolower', $array));
}

if (!in_arrayi($form_group, $valid_form_groups)) {
        array_push($errors, "Class group not valid.");
}



// Validate Gender.
$allowed_genders = ['male', 'female', 'other'];
if (!in_array($gender, $allowed_genders)) {
    array_push($errors, "Gender must be either Male or Female.");
}

// END | Validate Personal Details //
/////////////////////////////////////




////////////////////////////////////////
// START | Validate Subject Selection //

$unit1_selections_array = array();
$unit2_selections_array = array();

// Get and map unit 1 IDs and Names.
$result = $conn->query('SELECT unit_uid, subject_name FROM Subjects WHERE unit_no = 1 ORDER BY unit_uid');
while ($row = $result->fetch_assoc()) {
        foreach($unit1_selections as $selection_name) {
                if ($selection_name == $row['subject_name']) {
                        $unit1_selections_array[$row['unit_uid']] = $row['subject_name'];
                        //debug: echo $row['unit_uid']; echo $row['subject_name']; //debug
                }
        }
}
// Get and map unit 2 IDs and Names.
$result = $conn->query('SELECT unit_uid, subject_name FROM Subjects WHERE unit_no = 2 ORDER BY unit_uid');
while ($row = $result->fetch_assoc()) {
        foreach($unit2_selections as $selection_name) {
                if ($selection_name == $row['subject_name']) {
                        $unit2_selections_array[$row['unit_uid']] = $row['subject_name'];
                        //debug: echo $row['unit_uid']; echo $row['subject_name']; //debug
                }
        }
}



// Check that a unit hasn't been selected twice.
$selection_count = array_count_values($unit1_selections);
foreach ($selection_count as $item => $count) {
    if ($count > 1) {
                array_push($errors, "There is one or more duplicate unit 1 selections.");
    }
}
$selection_count = array_count_values($unit2_selections);
foreach ($selection_count as $item => $count) {
    if ($count > 1) {
        array_push($errors, "There is one or more duplicate unit 2 selections.");
    }
}



// Check that ESL and English subjects haven't both been selected.
function get_count($item, $list) {
        // Get an associative array with the count of each item
        $item_counts = array_count_values($list);

        // Check how many times the specific item appears
        $count = isset($item_counts[$item]) ? $item_counts[$item] : 0;

        return $count;
}

$count_u1_english = get_count("English", $unit1_selections);
$count_u1_esl = get_count("ESL", $unit1_selections);
$count_u2_english = get_count("English", $unit2_selections);
$count_u2_esl = get_count("ESL", $unit2_selections);

if ($count_u1_english != 0) {
        if ($count_u1_esl != 0 || $count_u2_esl != 0) {
                array_push($errors, "English and ESL subjects are mutually exclusive.");
        }
} elseif ($count_u1_esl != 0) {
        if ($count_u1_english != 0 || $count_u2_english != 0) {
                array_push($errors, "English and ESL subjects are mutually exclusive.");
        }
} elseif ($count_u2_english != 0) {
        if ($count_u1_esl != 0 || $count_u2_esl != 0) {
                array_push($errors, "English and ESL subjects are mutually exclusive.");
        }
} elseif ($count_u2_english != 0) {
        if ($count_u1_esl != 0 || $count_u2_esl != 0) {
                array_push($errors, "English and ESL subjects are mutually exclusive.");
    }
}



// Check if unit selections have reached max enrollment count.
$max_students_per_unit = 20;

$stmt = $conn->prepare('SELECT COUNT(student_uid) AS Count FROM Enrollments WHERE unit_uid = ?');
$stmt->bind_param('s', $unit_uid);

foreach ($unit1_selections_array as $unit_uid => $unit_name) {
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
        if ($count >= $max_students_per_unit) {
                array_push($errors, "Subject '$unit_name' in Unit 1 has reached its enrollment limit of $max_students_per_unit.");
        }
}

foreach ($unit2_selections_array as $unit_uid => $unit_name) {
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
        if ($count >= $max_students_per_unit) {
                array_push($errors, "Subject '$unit_name' in Unit 2 has reached its enrollment limit of $max_students_per_unit.");
        }
}

$stmt->close();

// END | Validate Subject Selection //
//////////////////////////////////////





//////////////////////////////////////
// START | Insert Form Data Into DB //

// If there are errors, exit redirect back with errors.
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
        $err_msg = "'" . implode('<br>', $errors) . "'";

        echo "<script>
        alert($err_msg);
        location.href='enrol.php';
        </script>";
        exit;
}



// Insert unit 1 and unit 2 selections.
$stmt = $conn->prepare('INSERT INTO Enrollments VALUES (?,?)');
$stmt->bind_param('ii', $_SESSION['id'], $unit_uid);

foreach($unit1_selections_array as $unit_uid => $unit_name) {
        $stmt->execute();
}

foreach($unit2_selections_array as $unit_uid => $unit_name) {
        $stmt->execute();
}
$stmt->close();



//insert personal form info into database.
$stmt = $conn->prepare('UPDATE Students SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, class_group = ? WHERE student_uid = ?');
$stmt->bind_param('ssssss',$first_name,$last_name,$dob,$gender,$form_group,$_SESSION['id']);
$stmt->execute();
$stmt->close();

// END | Insert Form Data Into DB //
////////////////////////////////////




// Close database connection.
$conn->close();

// Enrollment confirmation popup.
echo '<script>
        alert("You have enrolled succesfully!");
                location.href="home.php";
        </script>';
exit;

?>
