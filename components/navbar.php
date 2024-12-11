<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


include_once dirname(__DIR__) . '/config/config.php';
?>

<header class="header">
    <nav class="navbar">
        <div class="navbar__container">
            <div class="navbar__brand">
                <a href="<?php echo URL; ?>" class="navbar__logo">
                    <img src="<?php echo URL; ?>/assets/Logo.png" alt="Logo">
                </a>
            </div>

            <button class="navbar__toggle" aria-label="Toggle navigation">
                <span class="navbar__toggle-icon"></span>
            </button>

            <div class="navbar__menu">
                <ul class="navbar__list">
                    <li class="navbar__item">
                        <a href="<?php echo URL; ?>" class="navbar__link">Home</a>
                    </li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="navbar__item">
                            <a href="<?php echo URL; ?>/analytics.php" class="navbar__link">Analytics</a>
                        </li>
                        <li class="navbar__item">
                            <a href="<?php echo URL; ?>/bookings.php" class="navbar__link">My Bookings</a>
                        </li>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <li class="navbar__item">
                                <a href="<?php echo URL; ?>/dashboard.php" class="navbar__link">Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="navbar__actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="navbar__user">
                        <div class="navbar__user-info" onclick="toggleUserMenu()">
                            <?php if (isset($_SESSION['user']['username'])): ?>
                                <span class="navbar__username">
                                    <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                                </span>
                            <?php elseif (isset($_SESSION['user']['email'])): ?>
                                <span class="navbar__username">
                                    <?php echo htmlspecialchars(explode('@', $_SESSION['user']['email'])[0]); ?>
                                </span>
                            <?php endif; ?>

                            <img src="<?php echo URL . '/' .
                                (isset($_SESSION['user']['image']) && !empty(trim($_SESSION['user']['image']))
                                    ? $_SESSION['user']['image']
                                    : 'assets/userimages/default-profile.jpg'); ?>" alt="Profile Picture"
                                class="navbar__user-image">
                        </div>
                        <div class="navbar__user-menu" id="userMenu">
                            <a href="<?php echo URL; ?>/profile.php" class="navbar__btn navbar__btn--primary">Profile</a>
                            <a href="<?php echo URL; ?>/handlers/logout_handler.php" class="navbar__btn navbar__btn--outline">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="navbar__auth">
                        <a href="<?php echo URL; ?>/login.php" class="navbar__btn navbar__btn--outline">Sign In</a>
                        <a href="<?php echo URL; ?>/signup.php" class="navbar__btn navbar__btn--primary">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
            <button class="navbar__theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <svg class="icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>

    </nav>
</header>