<?php
// Assumes $content contains an array of 'items', each with 'question' and 'answer'
$items = $content['items'] ?? [];
$section_title_en = $content['title_en'] ?? 'Frequently Asked Questions';
$section_title_bn = $content['title_bn'] ?? 'সাধারণ জিজ্ঞাসা';
$title = ($_SESSION['lang'] ?? 'bn') === 'en' ? $section_title_en : $section_title_bn;
?>
<div class="faq-section" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($title); ?></h2>
    <div class="faq-accordion">
        <?php foreach ($items as $item): 
            $question = ($_SESSION['lang'] ?? 'bn') === 'en' ? ($item['question_en'] ?? '') : ($item['question_bn'] ?? '');
            $answer = ($_SESSION['lang'] ?? 'bn') === 'en' ? ($item['answer_en'] ?? '') : ($item['answer_bn'] ?? '');
        ?>
        <div class="faq-item">
            <button class="faq-question"><?php echo htmlspecialchars($question); ?></button>
            <div class="faq-answer">
                <p><?php echo nl2br(htmlspecialchars($answer)); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>