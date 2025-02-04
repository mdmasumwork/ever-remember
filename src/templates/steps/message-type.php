<?php
    $card_data = array(
        array(
            'header_icon' => 'sunflower.svg',
            'title' => 'A message for cards or flowers',
            'body_content' => 'Help me express my condolences in a short message',
            'type'          => 'condolence-message',
            'dimantion'   => '34px',
            'short_title' => 'condolence message'
        ),
        array(
            'header_icon' => 'email-orange.svg',
            'title' => 'A letter of sympathy',
            'body_content' => 'Help me write a longer letter of sympathy to someone I care about',
            'type'          => 'sympathy-letter',
            'dimantion'   => '36px',
            'short_title' => 'sympathy letter'
        ),
        array(
            'header_icon' => 'book.svg',
            'title' => 'A eulogy',
            'body_content' => 'Help me write a heartfelt eulogy to honor and remember <span class="deceased-person-name"></span>',
            'type'          => 'eulogy',
            'dimantion'   => '40px',
            'short_title' => 'eulogy'
        ),
        array(
            'header_icon' => 'badge.svg',
            'title' => 'An obituary or something else',
            'body_content' => 'Help me write a social post, an obituary, or anything else',
            'type'          => 'obituary',
            'dimantion'   => '32px',
            'short_title' => 'obituary'
        )
    )
?>
<section class="step step-message-type card-step">
    <h4>What kind of message would you like me to write?</h4>
    <div class="card-area">
        <?php
            foreach ($card_data as $index => $card) {
                ?>
                <div class="card go-next-1" 
                     data-message-type="<?= $card['short_title']; ?>"
                     tabindex="<?= $index; ?>" 
                     role="button"
                     aria-label="Select <?= $card['title']; ?>">
                    <div class="card-content">
                        <div class="card-header">
                            <img src="assets/images/icons/<?= $card['header_icon']; ?>" 
                                 width="<?= $card['dimantion']; ?>" 
                                 height="<?= $card['dimantion']; ?>">
                            <h4 class="card-title"><?= $card['title']; ?></h4>
                        </div>
                        <div class="card-body">
                            <p><?= $card['body_content']; ?></p>
                        </div>
                    </div>
                    <img src="assets/images/icons/right-arrow-orange.svg" class="card-arrow">
                </div>
                <?php
            }
        ?>
    </div>
</section>