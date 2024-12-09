<?php
session_start();
include_once 'config/config.php';


if(isset($_SESSION['user'])){
    header('Location: index.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - University Room Booking</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="js/theme.js"></script>
</head>
<body>
    <?php include_once 'components/navbar.php'; ?>
    
    <main class="login">
        <div class="login__container">
            <div class="login__content">
                <div class="login__header">
                    <h1 class="login__title">Welcome Back</h1>
                    <p class="login__subtitle">Login to book your classroom</p>
                </div>

                <form class="login__form" action="handlers/login_handler.php" method="POST">
                    <div class="login__field">
                        <label for="email" class="login__label">Email</label>
                        <input type="email" id="email" name="email" class="login__input" required>
                    </div>

                    <div class="login__field">
                        <label for="password" class="login__label">Password</label>
                        <input type="password" id="password" name="password" class="login__input" required>
                    </div>

                    <div class="login__options">
                        <label class="login__remember">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <?php if (isset($_SESSION['error_login'])): ?>
                        <div class="login__error">
                            <?php 
                                echo $_SESSION['error_login'];
                                unset($_SESSION['error_login']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="login__submit">Sign In</button>
                </form>

                <div class="login__footer">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>