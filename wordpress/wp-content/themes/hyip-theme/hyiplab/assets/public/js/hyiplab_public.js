jQuery(document).ready(function($){
    "user strict";
    
    // Overlay Event
    var over = $(".overlay");
    over.on("click", function () {
        $(".menu").removeClass("active");
        $(".overlay").removeClass("active");
    });
    
    $(".sidebar-menu li a").on("click", function (e) {
        $(".sidebar-submenu").removeClass("active");
        $(this).siblings(".sidebar-submenu").toggleClass("active");
    });
    
    $(".sidebar-submenu").parent().addClass("has-submenu");
    
    let elem = document.documentElement;
    /* View in fullscreen */
    function openFullscreen() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            /* Firefox */
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            /* Chrome, Safari and Opera */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            /* IE/Edge */
            elem.msRequestFullscreen();
        }
    }
    
    /* Close fullscreen */
    function closeFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            /* IE/Edge */
            document.msExitFullscreen();
        }
    }
    
    $(".dash-sidebar-toggler").on("click", function () {
        $(".dashboard-sidebar").toggleClass("active");
    });
    
    $(".dash-sidebar-close").on("click", function () {
        $(".dashboard-sidebar").removeClass("active");
    });
    
    let clickOne = document.getElementById("dashboard-sidebar");
    if (clickOne) {
        $("body").on("click", function (e) {
            let clickTwo = document.getElementById("dash-sidebar-toggler");
    
            if (clickOne !== e.target && !clickOne.contains(e.target) && clickTwo !== e.target && !clickTwo.contains(e.target)) {
                $(".dashboard-sidebar").removeClass("active");
            }
        });
    }
    
    function copyText() {
        var copyText = document.getElementById("referralURL");
        var copyText = document.getElementById("ref-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
    }
});