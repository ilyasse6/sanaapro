$(document).ready(function() {
    $('#sidebar-toggle').click(function() {
        $('.sidebar').toggleClass('active');
        $('.main-content').toggleClass('active');
    });
});
