class Toast {
    static init() {
        this.createToastElement();
    }

    static createToastElement() {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = 'Content copied!';
        document.body.appendChild(toast);
        this.toastElement = toast;
    }

    static show($text = 'Content copied!') {
        this.toastElement.textContent = $text;
        this.toastElement.classList.add('show');
        setTimeout(() => {
            this.toastElement.classList.remove('show');
        }, 2000);
    }

    static changeText($text) {
        this.toastElement.textContent = $text;
    }
}