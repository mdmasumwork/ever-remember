document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.primary-button');
    const inputFields = document.querySelectorAll('input');
    const textAreas = document.querySelectorAll('textarea');
    let formData = {};

    function sectionSubmit(event) {
        console.log('Section submit triggered');

        const section = event.target.closest('section');
        const inputFields = section.querySelectorAll('input, textarea, select');

        inputFields.forEach(field => {
            switch (field.id) {
                case 'first-person-name-field':
                    formData.firstPersonName = field.value;
                    break;
                case 'email-field':
                    formData.email = field.value;
                    break;
                case 'deceased-person-name-field':
                    formData.deceasedPersonName = field.value;
                    break;
                case 'message-type-input':
                    formData.messageType = field.value;
                    break;
                case 'deceased-person-relation-field':
                    formData.deceasedPersonRelation = field.value;
                    break;
                case 'deceased-person-details-field':
                    formData.deceasedPersonDetails = field.value;
                    break;
                case 'deceased-person-accomplishment-field':
                    formData.deceasedPersonAccomplishment = field.value;
                    break;
                case 'message-tone-input':
                    formData.messageTone = field.value;
                    break;
                case 'final-question-field':
                    formData.finalQuestion = field.value;
                    break;
                default:
                    break;
            }
        });

        console.log(formData);
        
        // Check if this is the final question section
        if (section.id === 'section-final-question') {
            console.log('Final section submitted, calling API');
            
            fetch('/api/generate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('API Response:', data);
                // Handle the generated content
                // Maybe redirect to a results page or show in a new section
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else {
            console.log('Regular section submit, moving to next section');
            // goToNextSection();
        }
    }

    // Attach event listeners to buttons
    buttons.forEach(function(button) {
        button.addEventListener('click', sectionSubmit);
    });

    // Attach event listeners to input fields
    inputFields.forEach(function(field) {
        field.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                sectionSubmit(event);
            }
        });
    });

    // Attach event listeners to text areas
    textAreas.forEach(function(area) {
        area.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                sectionSubmit(event);
            }
        });
    });

    // When the user clicks on a card, or presses enter with a card focused, set the formData messageType from the attribute data-message-type
    const messageTypeCards = document.querySelectorAll('.section-message-type .card');
    messageTypeCards.forEach(function(card) {
        card.addEventListener('click', function(event) {
            const messageType = card.getAttribute('data-message-type');
            formData.messageType = messageType;
            console.log(formData);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const selectedMessageTypeCard = document.querySelector('.section-message-type .card.selected');
            if (selectedMessageTypeCard) {
                const messageType = selectedMessageTypeCard.getAttribute('data-message-type');
                formData.messageType = messageType;
                console.log(formData);
            }
        }
    });

    // When the user clicks on a card, or presses enter with a card focused, set the formData messageTone from the attribute data-message-tone
    const messageToneCards = document.querySelectorAll('.section-message-tone .card');
    messageToneCards.forEach(function(card) {
        card.addEventListener('click', function(event) {
            const messageTone = card.getAttribute('data-message-tone');
            formData.messageTone = messageTone;
            console.log(formData);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const selectedMessageToneCard = document.querySelector('.section-message-tone .card.selected');
            if (selectedMessageToneCard) {
                const messageTone = selectedMessageToneCard.getAttribute('data-message-tone');
                formData.messageTone = messageTone;
                console.log(formData);
            }
        }
    });
});