document.addEventListener('DOMContentLoaded', function() {

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = 'Content copied!';
    document.body.appendChild(toast);

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabContainer = button.closest('.content-tabs');
            const selectedTabClass = button.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabContainer.querySelectorAll('.tab-button').forEach(btn => 
                btn.classList.remove('active')
            );
            tabContainer.querySelectorAll('.tab-pane').forEach(pane => 
                pane.classList.remove('active')
            );
            
            // Add active class to clicked button and corresponding pane
            button.classList.add('active');
            tabContainer.querySelectorAll('.tab-pane.' + selectedTabClass)[0].classList.add('active');
        });
    });

    // Handle copy icon clicks
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
            
            // Show feedback
            icon.classList.add('copied');
            toast.classList.add('show');
            
            setTimeout(() => {
                icon.classList.remove('copied');
                toast.classList.remove('show');
            }, 2000);
        });
    });
});