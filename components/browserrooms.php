
<section class="browser">
    <div class="browser__container">
        <div class="browser__header">
            <h2 class="browser__title">Available Rooms</h2>
            <div class="browser__search">
                <input type="text" id="searchInput" placeholder="Search rooms..." 
                       class="browser__search-input">
            </div>
        </div>

        <div class="browser__grid" id="roomsGrid">
            <?php 
            include_once dirname(__DIR__) . '/config/config.php';
            include_once dirname(__DIR__) . '/components/roomcard.php';
            
            try {
                $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $stmt = $conn->prepare("SELECT * FROM roomtable");
                $stmt->execute();

                while ($room = $stmt->fetch()) {
                    $roomCard = new Roomcard(
                        $room['room_id'], 
                        $room['roomtitle'], 
                        $room['roomimage'],
                        $room['roomseats'],
                        $room['roomprojectors'],
                        $room['roomwhiteboards']
                    );
                    echo $roomCard->display();
                }
            } catch (PDOException $e) {
                echo "<div class='browser__error'>Unable to load rooms</div>";
            }
            ?>
        </div>
    </div>
</section>