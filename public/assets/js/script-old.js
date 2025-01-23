// Select elements
const burgerMenu = document.getElementById("burger-menu");
const closeMenu = document.getElementById("close-menu");
const slideMenu = document.getElementById("slide-menu");

// Open the slide menu
burgerMenu.addEventListener("click", () => {
    slideMenu.classList.add("open");
});

// Close the slide menu
closeMenu.addEventListener("click", () => {
    slideMenu.classList.remove("open");
});

// Focus on the input field of the active section
function focusInputField(section) {
    const input = section.querySelector('input, textarea');

    if (input) {
        input.focus();
    } else {
        const primaryButton = section.querySelector('.er-button.primary-button');
        if (primaryButton) {
            primaryButton.focus();
        } else {
            const firstCard = section.querySelector('.card');
            if (firstCard) {
                firstCard.classList.add('focused');
            }
        }
    }
}

// Set focus on the input field of the first section on page load
document.addEventListener('DOMContentLoaded', () => {
    const firstSection = document.querySelector('section.active');
    if (firstSection) {
        focusInputField(firstSection);
    }
});

// Helper function to go to the next section
function goToNextSection() {
    const currentSection = document.querySelector('section.active');
    const nextSection = currentSection.nextElementSibling;
    
    if (!nextSection) return; // Stop if no next section
    
    // Transition current section out
    currentSection.classList.remove('active');
    currentSection.classList.add('slide-up');
    
    // Transition next section in
    nextSection.classList.add('active');
    nextSection.classList.remove('slide-down');
    
    // Focus on the input field of the next section
    focusInputField(nextSection);
    
    // Update progress bar
    const currentBar = document.querySelector('.progress-bar-item.active');
    if (currentBar.nextElementSibling) {
        currentBar.classList.remove('active');
        currentBar.nextElementSibling.classList.add('active');
    }
}

// Add event listeners to buttons and input fields
document.querySelectorAll('section .er-button.primary-button').forEach((button) => {
    button.addEventListener('click', () => {
        const input = button.closest('.field-area')?.querySelector('input, textarea'); // Find the closest input field
        // if (input) {
        //     console.log('Input found');
        //     if (input.value.trim()) {
        //         console.log('Input has value');
        //         if (input.id === 'first-person-name-field') {
        //             updateUserValues();
        //         }
        //         goToNextSection();
        //     } else {
        //         alert('Please fill out the field.');
        //     }
        // } else {
        //     goToNextSection();
        // }

        goToNextSection();
    });
});

document.querySelectorAll('section input, section textarea').forEach((element, index) => {
    element.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            if (element.tagName === 'TEXTAREA' && e.shiftKey) {
                // Allow new line in textarea with Shift+Enter
                return;
            }
            e.preventDefault();
            if (element.value.trim()) {
                if (element.id === 'first-person-name-field') {
                    updateUserValues();
                }
                goToNextSection();
            } else {
                alert('Please fill out the field.');
            }
        }
    });
});

const updateUserValues = () => {
    const inputValue = document.getElementById('first-person-name-field').value.trim();
    if (inputValue) {
        document.querySelectorAll('.first-person-name').forEach(element => {
            element.textContent = inputValue;
        });
    }
};

document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
        const focusedCard = document.querySelector('.card.focused');
        if (focusedCard) {
            e.preventDefault();
            if (e.shiftKey) {
                // Handle Shift+Tab
                const prevCard = focusedCard.previousElementSibling;
                if (prevCard && prevCard.classList.contains('card')) {
                    focusedCard.classList.remove('focused');
                    prevCard.classList.add('focused');
                    prevCard.focus();
                }
            } else {
                // Handle Tab
                const nextCard = focusedCard.nextElementSibling;
                if (nextCard && nextCard.classList.contains('card')) {
                    focusedCard.classList.remove('focused');
                    nextCard.classList.add('focused');
                    nextCard.focus();
                }
            }
        }
    }
});

// Handle Enter key press on focused card
document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        const focusedCard = document.querySelector('.card.focused');
        if (focusedCard) {
            e.preventDefault();
            focusedCard.classList.remove('focused');
            focusedCard.classList.add('selected');
            goToNextSection();
        }
    }
});

// Handle click on a card
document.querySelectorAll('.card').forEach((card) => {
    card.addEventListener('click', () => {
        card.classList.remove('focused');
        card.classList.add('selected');
        goToNextSection();
    });
});

// Code for moving the 'focused' class on the hovered card.
document.querySelectorAll('.card').forEach((card) => {
    card.addEventListener('mouseover', () => {
        document.querySelectorAll('.card').forEach((card) => {
            card.classList.remove('focused');
        });
        card.classList.add('focused');
    });
});