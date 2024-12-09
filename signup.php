<?php
session_start();
include_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - University Room Booking</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="js/theme.js"></script>
</head>
<body>
    <?php include_once 'components/navbar.php'; ?>
    
    <main class="signup">
        <div class="signup__container">
            <div class="signup__content">
                <div class="signup__header">
                    <h1 class="signup__title">Create Account</h1>
                    <p class="signup__subtitle">Join us to book university rooms</p>
                </div>

                <form class="signup__form" action="handlers/signup_handler.php" method="POST" enctype="multipart/form-data">
                    <div class="signup__grid">
                        <div class="signup__field">
                            <label for="firstname" class="signup__label">First Name</label>
                            <input type="text" id="firstname" name="firstname" class="signup__input" required>
                        </div>

                        <div class="signup__field">
                            <label for="lastname" class="signup__label">Last Name</label>
                            <input type="text" id="lastname" name="lastname" class="signup__input" required>
                        </div>
                    </div>

                    <div class="signup__field">
                        <label for="email" class="signup__label">University Email</label>
                        <input type="email" id="email" name="email" class="signup__input" 
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                    </div>
                    <div class="signup__field">
                        <label for="username" class="signup__label">Username</label>
                        <input type="text" id="text" name="username" class="signup__input">
                    </div>

                    <div class="signup__field">
                        <label for="password" class="signup__label">Password</label>
                        <input type="password" id="password" name="password" class="signup__input" 
                               minlength="8" required>
                        <small class="signup__hint">Minimum 8 characters</small>
                    </div>

                    <div class="signup__field">
                        <label for="confirm_password" class="signup__label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="signup__input" required>
                    </div>

                    <?php if (isset($_SESSION['error_signup'])): ?>
                        <div class="signup__error">
                            <?php 
                                echo $_SESSION['error_signup'];
                                unset($_SESSION['error_signup']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['sucesss_signup'])): ?>
                        <div class="signup__sucesss">
                            <?php 
                                echo $_SESSION['sucesss_signup'];
                                unset($_SESSION['sucesss_signup']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="signup__submit">Create Account</button>
                </form>

                <div class="signup__footer">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>