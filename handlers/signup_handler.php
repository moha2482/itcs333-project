

<?php 

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include_once dirname(__DIR__) . '/config/config.php';

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];


if(empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)){
    $_SESSION['error_signup'] = "All fields are required";
    header('Location: ' . URL . '/signup.php');
    exit();
}

$fullname = $firstname . ' ' . $lastname;


if(!preg_match("/@stu\.uob\.edu\.bh$/", $email)) {
    $_SESSION['error'] = "Email must be a valid @stu.uob.edu.bh address";
    header("Location: " . BASE_URL . "/signup");
    exit();
}


if($password !== $confirm_password){
    $_SESSION['error_signup'] = "Passwords do not match";
    header('Location: ' . URL . '/signup.php');
    exit();
}

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");

    $stmt->bindParam(':email', $email);

    $stmt->execute();

    if($stmt->rowCount() > 0){
        $_SESSION['error_signup'] = "Email already exists";
        header('Location:  ../signup.php');
        exit();
    }


    $stmt = $conn->prepare("INSERT INTO users (fullname,username, email, password) VALUES (:fullname,:username, :email, :password)");

    $stmt->bindParam(':fullname', $fullname);

    $stmt->bindParam(':email', $email);

    $stmt->bindParam(':username', $username);

    $stmt->bindParam(':password', hash('sha256', $password));

    $stmt->execute();

    $_SESSION['sucesss_signup'] = "Account created successfully";
    header('Location: ../signup.php');
    exit();


} catch (PDOException $e) {
    $_SESSION['error_signup'] = "Error: " . $e->getMessage();
    header('Location:  ../signup.php');
    exit();

}


?>