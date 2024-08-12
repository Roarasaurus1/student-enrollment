<?php
    // Session handling.
    include 'session.php';

    // Page Title
    $title = 'My Profile';



    // DB connection //
    define('DB_SERVER', 'localhost');
    define('DB_STUDENTS', 'admin');
    define('DB_PASSWORD', 'Cisco99');
    define('DB_DATABASE', 'school');

    $conn = new mysqli(DB_SERVER,DB_STUDENTS,DB_PASSWORD,DB_DATABASE);

    // Check DB errors. (This doesn't work)
    if ($conn->connect_error) {exit('Failed to connect to MariaDB: ' . $conn->connect_error);}



    // Get profile info //
    $stmt = $conn->prepare('SELECT first_name, last_name, date_of_birth, gender, class_group FROM Students WHERE student_uid = ?');
    $stmt->bind_param('s', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $date_of_birth, $gender, $class_group);
    $stmt->fetch();
    $stmt->close();



    // Get unit 1 info //
    $stmt = $conn->prepare('
        SELECT Subjects.subject_name
        FROM Subjects, Enrollments
        WHERE (
        Enrollments.student_uid = ?
        AND Subjects.unit_uid = Enrollments.unit_uid
        AND Subjects.unit_no = "1")
        ');

    $stmt->bind_param('s', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($subject_name);

    // Fetch all results into an array.
    $subjects_1 = array();
    while ($stmt->fetch()) {
        $subjects_1[] = $subject_name;
    }
    $stmt->close();



    // Get unit 2 info //
    $stmt = $conn->prepare('
        SELECT Subjects.subject_name
        FROM Subjects, Enrollments
        WHERE (
            Enrollments.student_uid = ?
            AND Subjects.unit_uid = Enrollments.unit_uid
            AND Subjects.unit_no = "2")
    ');
    $stmt->bind_param('s', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($subject_name);

    // Fetch all results into an array.
    $subjects_2 = array();
    while ($stmt->fetch()) {
        $subjects_2[] = $subject_name;
    }
    $stmt->close();



    // Close DB connection.
    $conn->close();
?>


<!DOCTYPE html>
<html lang="en" class="h-100">
    <!-- Begin Head -->
    <?php include 'templates/head.inc'; ?>
    <!-- End Head -->

    <body class="d-flex flex-column h-100">
        <!-- Begin Navbar -->
        <?php include 'templates/navbar.inc'; ?>
        <!-- End Navbar -->


        <!-- Begin page content -->
    <main class="container d-flex flex-column">
        <div class="d-flex flex-column">
                <p class="h1">Profile</p>
        </div>
        <div class="d-flex flex-column">

            <h2 class="mt-5">Account Details</h2>
            <table class="table">
                <tbody>
                    <tr>
                        <th scope="row" >Student ID:</th>
                        <td><?=htmlspecialchars($_SESSION['id'], ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <th scope="row">First Name:</th>
                        <td><?=htmlspecialchars($first_name, ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <th scope="row">Last Name:</th>
                        <td><?=htmlspecialchars($last_name, ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <th scope="row">Date of Birth:</th>
                        <td><?=htmlspecialchars($date_of_birth, ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                                                <th scope="row">Gender:</th>
                                                <td><?=htmlspecialchars($gender, ENT_QUOTES)?></td>
                    </tr>
                    <tr>
                        <th scope="row">Class Group:</th>
                        <td><?=htmlspecialchars($class_group, ENT_QUOTES)?></td>
                    </tr>
                </tbody>
            </table>

            <h2 class="mt-5">Class details</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Semester 1</th>
                        <th scope="col">Semester 2</th>
                    </tr>
                </thead>
                <tbody>
					<?php
						$maxSubjects = max(count($subjects_1), count($subjects_2));
						for ($i = 0; $i < $maxSubjects; $i++) {
							echo '<tr><td>';
							if (isset($subjects_1[$i])) {
								echo htmlspecialchars($subjects_1[$i]);
							}
							echo '</td>';
							echo '<td>';
							if (isset($subjects_2[$i])) {
								echo htmlspecialchars($subjects_2[$i]);
							}
							echo '</td></tr>';
						}
						?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- End page content -->


    <!-- Begin Page Footer -->
    <?php include 'templates/footer.inc'; ?>
    <!-- End Page Footer -->


    </body>
</html>
