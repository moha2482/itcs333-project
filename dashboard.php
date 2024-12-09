<?php
include_once 'config/config.php';
session_start();

// Admin access check
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ' . URL . '/login');
    exit();
}

// Fetch rooms
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $roomStmt = $conn->query("SELECT * FROM roomtable ORDER BY room_id DESC");
    $rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

    $commentStmt = $conn->prepare("
        SELECT c.*, u.username, r.roomtitle 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        JOIN roomtable r ON c.room_id = r.room_id 
        ORDER BY c.created_at DESC 
        LIMIT 5
    ");
    $commentStmt->execute();
    $recentComments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="js/theme.js"></script>
</head>
<body>
    <?php include_once 'components/navbar.php'; ?>

    <div class="dashboard">
        <div class="dashboard__container">
            <div class="dashboard__header">
                <h1>Dashboard Overview</h1>
                <button class="dashboard__btn dashboard__btn--primary" onclick="showAddRoomForm()">
                     Add Room
                </button>
            </div>

            <div id="roomForm" class="dashboard__modal">
                <div class="dashboard__modal-content">
                    <div class="dashboard__modal-header">
                        <h2>Room Management</h2>
                        <button onclick="hideRoomForm()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="handlers/room_handler.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="room_id" id="roomId">
                        <div class="form-group">
                            <label>Room Title</label>
                            <input type="text" name="roomtitle" required>
                        </div>
                        <div class="form-group">
                            <label>Seats</label>
                            <input type="number" name="roomseats" required>
                        </div>
                        <div class="form-group">
                            <label>Projectors</label>
                            <input type="number" name="roomprojectors" required>
                        </div>
                        <div class="form-group">
                            <label>White boards</label>
                            <input type="number" name="roomwhiteboards" required>
                        </div>
                        <div class="form-group">
                            <label>Room Image</label>
                            <input type="file" name="roomimage" accept="image/*">
                        </div>
                        <div class="form-actions">
                            <button type="submit">Save Room</button>
                            <button type="button" onclick="hideRoomForm()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dashboard__grid">
                <div class="dashboard__section">
                    <div class="dashboard__section-header">
                        <h2>Room Management</h2>
                    </div>
                    <div class="dashboard__rooms-grid">
                        <?php foreach($rooms as $room): ?>
                            <div class="room-card">
                                <div class="room-card__image">
                                    <img src="<?php echo URL . '/' . $room['roomimage']; ?>" alt="Room image">
                                </div>
                                <div class="room-card__content">
                                    <h3><?php echo htmlspecialchars($room['roomtitle']); ?></h3>
                                    <div class="room-card__details">
                                        <span>Seats <?php echo htmlspecialchars($room['roomseats']); ?></span>
                                        <span>Projects <?php echo htmlspecialchars($room['roomprojectors']); ?></span>
                                        <span>Whiteboards <?php echo htmlspecialchars($room['roomwhiteboards']); ?></span>
                                    </div>
                                    <div class="room-card__actions">
                                        <button onclick="editRoom(<?php echo htmlspecialchars(json_encode($room)); ?>)" 
                                                class="room-card__btn room-card__btn--edit">Edit</button>
                                        <button onclick="deleteRoom(<?php echo $room['room_id']; ?>)" 
                                                class="room-card__btn room-card__btn--delete">Delete</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="dashboard__section">
                    <div class="dashboard__section-header">
                        <h2>Recent Comments</h2>
                    </div>
                    <div class="dashboard__comments">
                    <?php foreach($recentComments as $comment): ?>
                        <div class="comment-card">
                            <div class="comment-card__header">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <span>on <?php echo htmlspecialchars($comment['roomtitle']); ?></span>
                            </div>
                            <p class="comment-card__content"><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <small><?php echo date('F j, Y g:i a', strtotime($comment['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showAddRoomForm() {
        document.getElementById('roomId').value = '';
        document.getElementById('roomForm').classList.add('active');
        document.body.classList.add('modal-open');
    }

    function hideRoomForm() {
        document.getElementById('roomForm').classList.remove('active');
        document.body.classList.remove('modal-open');
    }

    function editRoom(room) {
        const form = document.getElementById('roomForm');
        form.classList.add('active');
        document.body.classList.add('modal-open');
        
        document.getElementById('roomId').value = room.room_id;
        form.querySelector('[name="roomtitle"]').value = room.roomtitle;
        form.querySelector('[name="roomseats"]').value = room.roomseats;
        form.querySelector('[name="roomwhiteboards"]').value = room.roomwhiteboards;
        form.querySelector('[name="roomprojectors"]').value = room.roomprojectors;
    }

    function deleteRoom(roomId) {
        if(confirm('Are you sure you want to delete this room?')) {
            window.location.href = `handlers/delete_room_handler.php?id=${roomId}`;
        }
    }
    </script>
</body>
</html>