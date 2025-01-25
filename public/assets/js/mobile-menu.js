$(document).ready(function() {
    const $burgerMenu = $("#burger-menu");
    const $closeMenu = $("#close-menu");
    const $slideMenu = $("#slide-menu");

    // Open the slide menu
    $burgerMenu.on("click", () => {
        $slideMenu.addClass("active");
    });

    // Close the slide menu
    $closeMenu.on("click", () => {
        $slideMenu.removeClass("active");
    });
});