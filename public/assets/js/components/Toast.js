class Toast {
    static init() {
    }

    static show($text = 'Content copied!') {
        $(".toast-message").text($text).slideDown(300).delay(2000).slideUp(300);
    }

    static changeText($text) {
        this.toastElement.textContent = $text;
    }
}