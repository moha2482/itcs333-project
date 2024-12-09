document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');    
    const currentTheme = localStorage.getItem('theme') 
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    themeToggle.addEventListener('click', () => {
        const newTheme = document.documentElement.getAttribute('data-theme') === 'light' 
                        ? 'dark' 
                        : 'light';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
});



function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('active');

    document.addEventListener('click', function(event) {
        const userInfo = document.querySelector('.navbar__user-info');
        const userMenu = document.getElementById('userMenu');
        
        if (!userInfo.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove('active');
        }
    });
}