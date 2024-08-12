<?php
        // Session handling.
        include 'session.php';

        // Page Title
        $title = 'Example Page';
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
            <div class="flex-row">
                <h1 class="mt-5">Header</h1> <!-- All but the first h1 in 'main' must have the mt-5 class. -->
                <p class="lead">Lead/subheading</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea comm>
            </div>
            <div class="flex-row">
                <h1 class="mt-5">Header</h1>
                <p class="lead">Lead/subheading</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea comm>
            </div>
        </main>
                <!-- End page content -->

                <!-- Begin Page Footer -->
                <?php include 'templates/footer.inc'; ?>
                <!-- End Page Footer -->
        </body>
</html>
