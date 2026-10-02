<?php
session_start();
$page_title = ucfirst(str_replace('_', ' ', basename($_SERVER['PHP_SELF'], '.php')));
include 'includes/main_header.php';
?>

<div class="container" style="padding: 100px 20px; text-align: center;">
    <h1><?php echo $page_title; ?></h1>
    <p>This page is currently under development. Stay tuned!</p>
    <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Go Home</a>
</div>

<?php include 'includes/main_footer.php'; ?>
