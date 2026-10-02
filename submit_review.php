<?php
// submit_review.php
include 'db_connect.php';
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../login.php");
//     exit();
// }

$order_id = $_GET['order_id'] ?? '';
// In a real app, verify order belongs to user and is delivered.

$page = 'orders';
$page_title = 'Leave a Review';
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Review | AgriMarket';
include 'includes/main_header.php';
?>
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        font-size: 2rem;
    }

    .rating input {
        display: none;
    }

    .rating label {
        cursor: pointer;
        color: #ddd;
    }

    .rating input:checked~label {
        color: gold;
    }

    .rating label:hover,
    .rating label:hover~label {
        color: gold;
    }
</style>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">

    <div class="glass-panel" style="max-width: 600px; margin: 0 auto;">
        <h3>Rate your experience for Order #<?php echo htmlspecialchars($order_id); ?></h3>

        <form action="save_review.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

            <div class="form-group-dashboard">
                <label>Rating</label>
                <div class="rating">
                    <input type="radio" name="rating" value="5" id="5"><label for="5">★</label>
                    <input type="radio" name="rating" value="4" id="4"><label for="4">★</label>
                    <input type="radio" name="rating" value="3" id="3"><label for="3">★</label>
                    <input type="radio" name="rating" value="2" id="2"><label for="2">★</label>
                    <input type="radio" name="rating" value="1" id="1"><label for="1">★</label>
                </div>
            </div>

            <div class="form-group-dashboard">
                <label>Your Feedback</label>
                <textarea name="comment" class="form-control-glass" rows="4"
                    placeholder="How was the product quality?"></textarea>
            </div>

            <button type="submit" class="btn-primary-glass">Submit Review</button>
        </form>
    </div>

</div>

<?php include 'includes/main_footer.php'; ?>

</html>