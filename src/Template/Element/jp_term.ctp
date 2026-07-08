<?php
/**
 * Bilingual domain-term chip. Pairs a page/entity with its proper
 * Japanese technical-intern-program term.
 *
 * Usage:
 *   <?= $this->element('jp_term', [
 *       'kanji' => '技能実習生', 'kana' => 'ぎのうじっしゅうせい',
 *       'romaji' => 'Ginō Jisshūsei', 'en' => 'Technical Intern Trainee',
 *   ]) ?>
 *
 * @var string $kanji   Japanese term (required)
 * @var string $kana    Furigana reading (optional)
 * @var string $romaji  Romanised reading (optional)
 * @var string $en      English gloss (optional)
 */
$kanji = isset($kanji) ? $kanji : '';
if ($kanji === '') {
    return;
}
$kana = isset($kana) ? $kana : '';
$romaji = isset($romaji) ? $romaji : '';
$en = isset($en) ? $en : '';
?>
<span class="jp-term" title="<?= h(trim($romaji . ($en ? ' — ' . $en : ''))) ?>">
    <span class="jp-kanji"><?= h($kanji) ?></span>
    <?php if ($kana): ?><span class="jp-kana">（<?= h($kana) ?>）</span><?php endif; ?>
    <?php if ($romaji): ?><span class="jp-sep">·</span><span class="jp-romaji"><?= h($romaji) ?></span><?php endif; ?>
    <?php if ($en): ?><span class="jp-sep">·</span><span class="jp-en"><?= h($en) ?></span><?php endif; ?>
</span>
