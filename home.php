<?php include 'session.php'; ?>
<?php $title = 'Home'; ?>

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
                <main class="d-flex flex-column h-100 justify-content-center col-lg-6 col-xxl-4 mx-auto">
                        <div class="d-grid gap-4">
                        <a href="enrol.php" class="btn btn-primary">Course Enrollment</a>
                        <a href="my-profile.php" class="btn btn-secondary">My Profile</a>
                        </div>
                </main>
                <!-- End page content -->

                <!-- Begin Page Footer -->
                <?php include 'templates/footer.inc'; ?>
                <!-- End Page Footer -->
        </body>
</html>
