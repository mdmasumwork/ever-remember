<?php
    $tone_data = array(
        array(
            'header_icon' => 'heart-hand.svg',
            'title' => 'Compassionate',
            'body_content' => 'Gentle and heartfelt, ideal for creating a comforting and personal message.',
            'type'          => 'compassionate',
            'dimantion'   => '30px'
        ),
        array(
            'header_icon' => 'man-giving-speech.svg',
            'title' => 'Formal',
            'body_content' => 'Professional and traditional, suitable for a more dignified tribute.',
            'type'          => 'formal',
            'dimantion'   => '38px'
        ),
        array(
            'header_icon' => 'man-holding-microphone.svg',
            'title' => 'Poetic',
            'body_content' => 'Elegant and expressive, focusing on deep emotions for an artistic touch.',
            'type'          => 'poetic',
            'dimantion'   => '26px'
        ),
        array(
            'header_icon' => 'uplifting.svg',
            'title' => 'Uplifting',
            'body_content' => 'Positive and hopeful, celebrating the joy and achievements of a life well-lived.',
            'type'          => 'uplifting',
            'dimantion'   => '34px'
        )
    )
?>
<section class="step step-message-tone card-step">
    <h4>Thank you for sharing these details about <span class='deceased-person-name-placeholder'>the deceased person</span>.</h4>
    <h4>To ensure the message truly honors them, could you let me know the tone that feels most appropriate? Here are some options to choose from:</h4>
    <div class="card-area">
        <?php foreach ($tone_data as $index => $tone): ?>
            <div class="card go-next-1" 
                 tabindex="<?= $index; ?>" 
                 role="button"
                 aria-label="<?= $tone['title']; ?>"
                 data-message-tone="<?= $tone['type']; ?>">
                <div class="card-content">
                    <div class="card-header">
                        <img src="assets/images/icons/<?= $tone['header_icon']; ?>"
                            width="<?= $tone['dimantion']; ?>" 
                            height="<?= $tone['dimantion']; ?>">
                        <h4 class="card-title"><?= $tone['title']; ?></h4>
                    </div>
                    <div class="card-body">
                        <p><?= $tone['body_content']; ?></p>
                    </div>
                </div>
                <img src="assets/images/icons/right-arrow-orange.svg" class="card-arrow">
            </div>
        <?php endforeach; ?>
    </div>
</section>