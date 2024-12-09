<?php
include_once  'config/config.php';
session_start();

$roomId = isset($_GET['id']) ? (int) $_GET['id'] : 1;

if(!isset($roomId) || empty($roomId)){
    header('Location: ' . URL);
    exit();
}

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM roomtable WHERE room_id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    $commentStmt = $conn->prepare("
        SELECT c.*, u.username, u.image as user_image 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.room_id = ? 
        ORDER BY c.created_at DESC
    ");
    $commentStmt->execute([$roomId]);
    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['roomtitle']); ?> - Room View</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="js/theme.js"></script>
</head>
<body>
    <?php include_once 'components/navbar.php'; ?>

    <main class="room">
        <div class="room__container">
            <section class="room__details">
                <div class="room__image">
                    <img src="<?php echo htmlspecialchars($room['roomimage']); ?>" 
                         alt="<?php echo htmlspecialchars($room['roomtitle']); ?>">
                </div>

                <div class="room__info">
                    <h1 class="room__title"><?php echo htmlspecialchars($room['roomtitle']); ?></h1>
                    
                    <div class="room__specs">
                        <div class="room__spec">
                            <i class="fas fa-users"></i>
                            <span><?php echo htmlspecialchars($room['roomseats']); ?> Seats</span>
                        </div>
                        <div class="room__spec">
                            <i class="fas fa-projector"></i>
                            <span><?php echo htmlspecialchars($room['roomprojectors']); ?> Projectors</span>
                        </div>
                        <div class="room__spec">
                            <i class="fas fa-projector"></i>
                            <span><?php echo htmlspecialchars($room['roomwhiteboards']); ?> Whiteboards</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="room__booking">
                <h2 class="room__booking-title">Book This Room</h2>
                <?php if (isset($_SESSION['user'])): ?>
                    <form class="room__form" action="handlers/booking_handler.php" method="POST">

                        <?php if (isset($_SESSION['error_booking'])): ?>
                            <p class="room__error"><?php echo $_SESSION['error_booking']; ?></p>
                            <?php unset($_SESSION['error_booking']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_booking'])): ?>
                            <p class="room__success"><?php echo $_SESSION['success_booking']; ?></p>
                            <?php unset($_SESSION['success_booking']); ?>
                        <?php endif; ?>
                        
                        <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                        
                        <div class="room__form-group">
                            <label class="room__form-label">Date</label>
                            <input type="date" name="booking_date" class="room__form-input" 
                                   required min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="room__form-group">
                            <label class="room__form-label">Time Slot</label>
                            <select name="time_slot" class="room__form-input" required>
                                <option value="">Select time</option>
                                <?php
                                $timeSlots = [
                                    '09:00' => '09:00 - 10:00',
                                    '10:00' => '10:00 - 11:00',
                                    '11:00' => '11:00 - 12:00',
                                    '12:00' => '12:00 - 13:00',
                                    '13:00' => '13:00 - 14:00',
                                    '14:00' => '14:00 - 15:00',
                                    '15:00' => '15:00 - 16:00',
                                    '16:00' => '16:00 - 17:00'
                                ];
                                foreach ($timeSlots as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="room__btn room__btn--primary">Book Now</button>
                    </form>
                <?php else: ?>
                    <p class="room__login-prompt">
                        Please <a href="<?php echo URL; ?>/login.php">login</a> to book this room.
                    </p>
                <?php endif; ?>
            </section>
            <section class="room__comments">
    <h2 class="room__comments-title">Comments</h2>
    
    <?php if (isset($_SESSION['user'])): ?>
        <form class="room__comment-form" action="handlers/comment_handler.php" method="POST">
            <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
            <textarea name="comment" class="room__comment-input" 
                      placeholder="Write your comment..." required></textarea>
            <button type="submit" class="room__btn room__btn--primary">Post Comment</button>
        </form>
    <?php endif; ?>

    <div class="room__comments-list">
        <?php
        $parentComments = array_filter($comments, function($comment) {
            return $comment['parent_id'] === null;
        });

        foreach ($parentComments as $comment):
            $replies = array_filter($comments, function($reply) use ($comment) {
                return $reply['parent_id'] === $comment['id'];
            });
        ?>
            <div class="room__comment">
                <div class="room__comment-user">
                    <img src="<?php echo URL . '/' . 
                        (isset($comment['user_image']) && !empty(trim($comment['user_image']))
                            ? $comment['user_image']
                            : 'assets/userimages/default-profile.jpg'); ?>" 
                         alt="User profile">
                    <span><?php echo htmlspecialchars($comment['username']); ?></span>
                </div>
                
                <div class="room__comment-content">
                    <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    <small><?php echo date('F j, Y', strtotime($comment['created_at'])); ?></small>
                    
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <button class="room__comment-reply" 
                                onclick="toggleReplyForm(<?php echo $comment['id']; ?>)">
                            Reply
                        </button>
                        
                        <form id="replyForm-<?php echo $comment['id']; ?>" 
                              class="room__reply-form hidden" 
                              action="handlers/comment_handler.php" 
                              method="POST">
                            <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                            <textarea name="comment" class="room__comment-input" 
                                      placeholder="Write a reply..." required></textarea>
                            <button type="submit" class="room__btn room__btn--secondary">Post Reply</button>
                        </form>
                    <?php endif; ?>

                    <?php foreach ($replies as $reply): ?>
                        <div class="room__reply">
                            <div class="room__reply-context">
                                Replying to <span><?php echo htmlspecialchars($comment['username']); ?></span>
                            </div>
                            
                            <div class="room__comment-user">
                                <img src="<?php echo URL . '/' . 
                                    (isset($reply['user_image']) && !empty(trim($reply['user_image']))
                                        ? $reply['user_image']
                                        : 'assets/userimages/default-profile.jpg'); ?>" 
                                     alt="User profile">
                                <span><?php echo htmlspecialchars($reply['username']); ?> (Admin)</span>
                            </div>
                            
                            <div class="room__comment-content">
                                <p><?php echo htmlspecialchars($reply['comment']); ?></p>
                                <small><?php echo date('F j, Y', strtotime($reply['created_at'])); ?></small>
                            </div>

                            <?php if (isset($_SESSION['user']) && 
                                    ($_SESSION['user']['role'] === 'admin' || 
                                     $_SESSION['user']['id'] === $reply['user_id'])): ?>
                                <button class="room__comment-delete" 
                                        onclick="confirmDelete(<?php echo $reply['id']; ?>)">
                                        DELETE REPLY
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($_SESSION['user']) && 
                        ($_SESSION['user']['role'] === 'admin' || 
                         $_SESSION['user']['id'] === $comment['user_id'])): ?>
                    <button class="room__comment-delete" 
                            onclick="confirmDelete(<?php echo $comment['id']; ?>)">
                            DELETE
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function toggleReplyForm(commentId) {
    const form = document.getElementById(`replyForm-${commentId}`);
    form.classList.toggle('hidden');
}

function confirmDelete(commentId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        window.location.href = `handlers/delete_comment_handler.php?id=${commentId}&room_id=<?php echo $roomId; ?>`;
    }
}
</script>
</body>
</html>