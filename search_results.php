<?php
include 'includes/main_header.php';
$query = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
$category = isset($_GET['cat']) ? htmlspecialchars($_GET['cat']) : 'all';
?>
<div class="container" style="padding: 100px 0; text-align: center;">
    <h1 style="color: #16a34a;"><i class="fas fa-search"></i> Search Results</h1>
    <p>Searching for <strong>
            <?php echo $query; ?>
        </strong> in category: <strong>
            <?php echo $category; ?>
        </strong></p>
    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 50px; border-radius: 20px; margin-top: 30px;">
        <p style="color: #64748b;">This is a placeholder for search results. Integration with PHP + MySQL logic for
            multi-table search can be added next.</p>
        <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Back to Home</a>
    </div>
</div>
<?php include 'includes/main_footer.php'; ?>