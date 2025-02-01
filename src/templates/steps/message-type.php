<?php
    $card_data = array(
        array(
            'header_icon' => 'sunflower.svg',
            'title' => 'A message for cards or flowers',
            'body_content' => 'Help me express my condolences in a short message',
            'type'          => 'condolence-message',
            'dimantion'   => '40px',
        ),
        array(
            'header_icon' => 'pen-tip.svg',
            'title' => 'A letter of sympathy',
            'body_content' => 'Help me write a longer letter of sympathy to someone I care about',
            'type'          => 'sympathy-letter',
            'dimantion'   => '48px',
        ),
        array(
            'header_icon' => 'man-reading-eulogy.svg',
            'title' => 'A eulogy',
            'body_content' => 'Help me write a heartfelt eulogy to honor and remember <span class="deceased-person-name"></span>',
            'type'          => 'eulogy',
            'dimantion'   => '38px',
        ),
        array(
            'header_icon' => 'message.svg',
            'title' => 'An obituary or something else',
            'body_content' => 'Help me write a social post, an obituary, or anything else',
            'type'          => 'obituary',
            'dimantion'   => '42px',
        )
    )
?>
<section class="step step-message-type">
    <h4>What kind of message would you like me to write?</h4>
    <div class="card-area">
        <?php
            foreach ($card_data as $index => $card) {
                ?>
                <div class="card go-next-1" 
                     data-message-type="<?= $card['type']; ?>"
                     tabindex="<?= $index; ?>" 
                     role="button"
                     aria-label="Select <?= $card['title']; ?>">
                    <div class="card-header">
                        <img src="assets/images/icons/<?= $card['header_icon']; ?>" 
                             width="<?= $card['dimantion']; ?>" 
                             height="<?= $card['dimantion']; ?>">
                        <h4 class="card-title"><?= $card['title']; ?></h4>
                    </div>
                    <div class="card-body">
                        <p><?= $card['body_content']; ?></p>
                    </div>
                    <img src="assets/images/icons/right-arrow-orange.svg" class="card-arrow">
                </div>
                <?php
            }
        ?>
    </div>
</section>