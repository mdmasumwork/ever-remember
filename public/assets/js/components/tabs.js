class Tabs {
    static init() {
        this.bindTabEvents();
        this.bindCopyEvents();
    }

    static bindTabEvents() {
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                const tabContainer = button.closest('.content-tabs');
                const selectedTabClass = button.getAttribute('data-tab');
                
                tabContainer.querySelectorAll('.tab-button').forEach(btn => 
                    btn.classList.remove('active')
                );
                tabContainer.querySelectorAll('.tab-pane').forEach(pane => 
                    pane.classList.remove('active')
                );
                
                button.classList.add('active');
                tabContainer.querySelectorAll('.tab-pane.' + selectedTabClass)[0].classList.add('active');
            });
        });
    }

    static bindCopyEvents() {
        document.querySelectorAll('.copy-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const tabPane = this.closest('.tab-pane');
                const contentBox = tabPane?.querySelector('.content-box');
                
                if (!contentBox) return;

                const textToCopy = contentBox.textContent.trim();
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                textarea.style.position = 'fixed';
                document.body.appendChild(textarea);
                textarea.select();
                
                document.execCommand('copy');
                document.body.removeChild(textarea);
                
                icon.classList.add('copied');
                Toast.show();
                
                setTimeout(() => {
                    icon.classList.remove('copied');
                }, 2000);
            });
        });
    }
}