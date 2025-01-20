// Toggle active state for sidebar links
document.querySelectorAll('.sidebar a').forEach((link) => {
    link.addEventListener('click', function () {
        document.querySelectorAll('.sidebar a').forEach((el) => el.classList.remove('active'));
        this.classList.add('active');
    });
});
