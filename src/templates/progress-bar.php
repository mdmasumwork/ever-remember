<div class="vertical-progress">
    <?php
    $steps = [
        'Introduction', 'Email', 'Disclaimer', 'Name', 'Type', 
        'Relation', 'Details', 'Additional info', 'Tone', 
        'Final Question', 'Content'
    ];
    foreach ($steps as $index => $step) {
        echo "<div class='vertical-progress__step'></div>";
    }
    ?>
</div>
