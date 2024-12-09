
<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}


include_once dirname(__DIR__) . '/config/config.php';


$email = $_POST['email'];
$password = $_POST['password'];

if(empty($email) || empty($password)) {
    $_SESSION['error_login'] = "Please fill in all fields";
    header('Location: '. URL .'/login.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");

    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch();

    $hashed_password = hash('sha256', $password);

    if($user['password'] === $hashed_password) {
        $_SESSION['user'] = $user;

        header("location: ../index.php");
        exit();
    } else {
        $_SESSION['error_login'] = "Invalid email or password";
        header('Location: '. URL .'/login.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_login'] = "Error: " . $e->getMessage();
    header('Location: '. URL .'/login.php');
    exit();
}



?>