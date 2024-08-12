<?php
    // Session handling.
    include 'session.php';

    // Page Title
    $title = 'Enrollment Form';



    // DB connection //
    define('DB_SERVER', 'localhost');
    define('DB_STUDENTS', 'admin');
    define('DB_PASSWORD', 'Cisco99');
    define('DB_DATABASE', 'school');

    $conn = new mysqli(DB_SERVER,DB_STUDENTS,DB_PASSWORD,DB_DATABASE);

    // Check DB errors. (This doesn't work)
    if ($conn->connect_error) {exit('Failed to connect to MariaDB: ' . $conn->connect_error);}



    // Get list of available units.
    $unit1_subjects = array();
    $unit2_subjects = array();

    $result = $conn->query('SELECT unit_uid, subject_name FROM Subjects WHERE unit_no = 1 ORDER BY unit_uid');
    while ($row = $result->fetch_assoc()) {
        $unit1_subjects[$row['unit_uid']] = $row['subject_name'];
    }

    $result = $conn->query('SELECT unit_uid, subject_name FROM Subjects WHERE unit_no = 2 ORDER BY unit_uid');
    while ($row = $result->fetch_assoc()) {
        $unit2_subjects[$row['unit_uid']] = $row['subject_name'];
    }
?>




<!DOCTYPE html>
<html lang="en" class="h-100">
    <!-- Begin Head -->
        <?php include 'templates/head.inc'; ?>
    <!-- End Head -->

    <body class="d-flex flex-column">
        <!-- Begin Navbar -->
        <?php include 'templates/navbar.inc'; ?>
        <!-- End Navbar -->

        <!-- Begin page content -->
        <main class="container d-flex flex-column">
        <h1 class="mb-5">Student Enrollment Form</h1>
        <form action="enrol-process.php" method="post" id="enrollmentForm">
            <!-- Unit 1 Selections -->
            <div id="unit1_selects">
                <h2>Unit 1 Selections</h2>
                <?php for ($i = 1; $i <= 5; $i++):  ?>
                <div class="mb-3">
                    <label for="unit1_<?php echo $i; ?>" class="form-label">Unit 1 Subject <?php echo $i; ?></label>
                    <select name="unit1[]" id="unit1_<?php echo $i; ?>" class="form-select unit1_select" required>
                        <option value="">Select Subject</option>
                        <!-- PHP code to populate options based on available Unit 1 subjects -->
                        <?php foreach ($unit1_subjects as $subject): ?>
                            <option value="<?php echo $subject; ?>"><?php echo $subject; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Unit 2 Selections -->
            <div id="unit2_selects">
                <h2 class="mt-5">Unit 2 Selections</h2>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="mb-3">
                    <label for="unit2_<?php echo $i; ?>" class="form-label">Unit 2 Subject <?php echo $i; ?></label>
                    <select name="unit2[]" id="unit2_<?php echo $i; ?>" class="form-select unit2_select" required>
                        <option value="">Select Subject</option>
                        <!-- PHP code to populate options based on available Unit 2 subjects -->
                        <?php foreach ($unit2_subjects as $subject): ?>
                            <option value="<?php echo $subject; ?>"><?php echo $subject; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endfor; ?>
            </div>

            <h2 class="mt-5">Personal Details</h2>

            <!-- First Name -->
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>

            <!-- Last Name -->
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>

            <!-- Date of Birth -->
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control" required>
            </div>

            <!-- Form Group -->
            <div class="mb-3">
                <label for="form_group" class="form-label">Form Group</label>
                <input type="text" name="form_group" id="form_group" class="form-control" required>
            </div>

            <!-- Gender -->
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Error Message Display -->
            <div id="error-message" class="alert alert-danger d-none" role="alert">
                English and ESL cannot be selected at the same time.
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        </main>
        <!-- End page content -->

        <!-- Begin Page Footer -->
        <?php include 'templates/footer.inc'; ?>
        <!-- End Page Footer -->
    </body>
</html>




<script>
$(document).ready(function() {
    // Arrays to store selected subjects
    var selectedUnit1Subjects = [];
    var selectedUnit2Subjects = [];

    // Function to update dropdown options based on selected subjects
    function updateDropdownOptions(unit, selectedSubjects) {
        // Reset options for the given unit
        $('#unit' + unit + '_selects .form-select').each(function() {
            var currentSelect = $(this);
            currentSelect.find('option').show(); // Show all options initially

            // Hide options that are already selected
            selectedSubjects.forEach(function(subject) {
                currentSelect.find('option[value="' + subject + '"]').hide();
            });
        });
    }

    // Function to validate and highlight invalid combinations
    function validateSelections() {
        var isInvalid = false;

        // Get selected subjects
        var englishSelected = selectedUnit1Subjects.includes('English') || selectedUnit2Subjects.includes('English');
        var eslSelected = selectedUnit1Subjects.includes('ESL') || selectedUnit2Subjects.includes('ESL');

        if (englishSelected && eslSelected) {
            isInvalid = true;
        }

        // Show or hide error message
        $('#error-message').toggleClass('d-none', !isInvalid);

        // Highlight invalid selects
        $('.form-select').each(function() {
            var select = $(this);
            var selectedValue = select.val();
            var isInvalid = (englishSelected && selectedValue === 'ESL') || (eslSelected && selectedValue === 'English');
            select.toggleClass('is-invalid', isInvalid);
        });
    }

    // Event handler for Unit 1 dropdown change
    $('#unit1_selects').on('change', '.form-select', function() {
        selectedUnit1Subjects = []; // Reset array
        $('#unit1_selects .form-select').each(function() {
            var subject = $(this).val();
            if (subject !== '') {
                selectedUnit1Subjects.push(subject);
            }
        });
        updateDropdownOptions(1, selectedUnit1Subjects);
        validateSelections();
    });

    // Event handler for Unit 2 dropdown change
    $('#unit2_selects').on('change', '.form-select', function() {
        selectedUnit2Subjects = []; // Reset array
        $('#unit2_selects .form-select').each(function() {
            var subject = $(this).val();
            if (subject !== '') {
                selectedUnit2Subjects.push(subject);
            }
        });
        updateDropdownOptions(2, selectedUnit2Subjects);
        validateSelections();
    });

    // Initial validation and update
    updateDropdownOptions(1, selectedUnit1Subjects);
    updateDropdownOptions(2, selectedUnit2Subjects);
    validateSelections();
});
</script>
