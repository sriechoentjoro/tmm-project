<?php
/**
 * @var \App\View\AppView $this
 * @var array  $guide        ['Category' => [['title','url','icon','parent'], ...]]
 * @var array  $roleNames
 * @var string $displayName
 */

// Category colour palette
$catColors = [
    'Training'    => ['bg' => '#0288D1', 'light' => '#E1F5FE', 'accent' => '#0277BD'],
    'Recruitment' => ['bg' => '#00897B', 'light' => '#E0F2F1', 'accent' => '#00796B'],
    'Reports'     => ['bg' => '#6949BC', 'light' => '#EDE7F6', 'accent' => '#5E35B1'],
    'Dashboards'  => ['bg' => '#1565C0', 'light' => '#E3F2FD', 'accent' => '#0D47A1'],
    'System'      => ['bg' => '#546E7A', 'light' => '#ECEFF1', 'accent' => '#455A64'],
    'Other'       => ['bg' => '#78909C', 'light' => '#F5F7F8', 'accent' => '#607D8B'],
];
$fallback = ['bg' => '#00838F', 'light' => '#E0F7FA', 'accent' => '#006064'];

$roleBadgeColors = [
    'administrator'    => '#c0392b',
    'management'       => '#8e44ad',
    'tmm-training'     => '#0288D1',
    'tmm-recruitment'  => '#00897B',
    'tmm-accounting'   => '#e67e22',
    'tmm-documentation'=> '#2980b9',
    'lpk-penyangga'    => '#27ae60',
];
?>

<style>
/* ── Guide page layout ─────────────────────────────────────────── */
.guide-page { max-width: 1100px; margin: 0 auto; padding: 8px 0 40px; }

/* Welcome banner */
.guide-banner {
    background: linear-gradient(135deg, #00838F 0%, #00BCD4 60%, #26C6DA 100%);
    border-radius: 16px;
    padding: 28px 36px;
    display: flex;
    align-items: center;
    gap: 22px;
    margin-bottom: 32px;
    box-shadow: 0 6px 24px rgba(0,188,212,0.22);
    position: relative;
    overflow: hidden;
}
.guide-banner::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
    pointer-events: none;
}
.guide-banner-icon {
    width: 64px; height: 64px;
    background: rgba(255,255,255,0.18);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.guide-banner-icon i { font-size: 30px; color: #fff; }
.guide-banner-text h2 { margin: 0 0 4px; color: #fff; font-size: 22px; font-weight: 700; }
.guide-banner-text p  { margin: 0; color: rgba(255,255,255,0.82); font-size: 14px; }
.role-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
.role-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    background: rgba(255,255,255,0.22);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.35);
}

/* Category section */
.guide-category { margin-bottom: 32px; }
.guide-cat-header {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 16px;
}
.guide-cat-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}
.guide-cat-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1.4px;
}
.guide-cat-line {
    flex: 1; height: 1px;
    background: #e2e8f0;
}

/* Feature grid */
.guide-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 14px;
}

/* Feature card */
.guide-card {
    background: #fff;
    border: 1.5px solid #e8edf3;
    border-radius: 12px;
    padding: 18px 16px 14px;
    text-decoration: none;
    display: flex; flex-direction: column;
    gap: 10px;
    transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
    position: relative;
    overflow: hidden;
}
.guide-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    text-decoration: none;
}
.guide-card-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.guide-card-icon i { font-size: 18px; }
.guide-card-body { flex: 1; }
.guide-card-title {
    font-size: 13px; font-weight: 700;
    color: #1a202c;
    margin: 0 0 3px;
    line-height: 1.35;
}
.guide-card-parent {
    font-size: 11px; color: #94a3b8;
    font-weight: 500;
}
.guide-card-arrow {
    font-size: 11px; color: #cbd5e0;
    align-self: flex-end;
    transition: color 0.18s, transform 0.18s;
}
.guide-card:hover .guide-card-arrow { color: inherit; transform: translateX(3px); }

/* Empty state */
.guide-empty {
    text-align: center; padding: 60px 20px;
    color: #94a3b8;
}
.guide-empty i { font-size: 48px; margin-bottom: 16px; display: block; }

@media (max-width: 576px) {
    .guide-banner { padding: 20px; gap: 14px; }
    .guide-grid   { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="guide-page">

    <!-- Welcome banner -->
    <div class="guide-banner">
        <div class="guide-banner-icon">
            <i class="fas fa-map-signs"></i>
        </div>
        <div class="guide-banner-text">
            <h2><?= __('Welcome, {0}', h($displayName)) ?></h2>
            <p><?= __('Here are all the management functions and features available for your role. Click any card to get started.') ?></p>
            <?php if (!empty($roleNames)): ?>
            <div class="role-badges">
                <?php foreach ($roleNames as $rn):
                    $bc = isset($roleBadgeColors[$rn]) ? $roleBadgeColors[$rn] : 'rgba(255,255,255,0.3)';
                ?>
                <span class="role-badge" style="background:<?= h($bc) ?>88; border-color:<?= h($bc) ?>;">
                    <i class="fas fa-user-tag" style="font-size:10px;"></i>
                    <?= h($rn) ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($guide)): ?>
    <div class="guide-empty">
        <i class="fas fa-inbox"></i>
        <p><?= __('No features have been assigned to your role yet. Please contact your administrator.') ?></p>
    </div>
    <?php else: ?>

    <?php foreach ($guide as $catName => $items):
        $col = isset($catColors[$catName]) ? $catColors[$catName] : $fallback;
    ?>
    <div class="guide-category">

        <!-- Category header -->
        <div class="guide-cat-header">
            <div class="guide-cat-dot" style="background:<?= $col['bg'] ?>;"></div>
            <span class="guide-cat-label" style="color:<?= $col['accent'] ?>;"><?= h($catName) ?></span>
            <div class="guide-cat-line"></div>
            <span style="font-size:11px;color:#94a3b8;white-space:nowrap;"><?= count($items) ?> <?= __('feature(s)') ?></span>
        </div>

        <!-- Feature cards -->
        <div class="guide-grid">
            <?php foreach ($items as $item):
                $webroot = $this->request->getAttribute('webroot');
                $href    = $item['url'];
                if ($href && strpos($href, 'http') !== 0 && strpos($href, '/') === 0) {
                    $href = $webroot . ltrim($href, '/');
                }
                $icon = $item['icon'] ?: 'fa-circle';
            ?>
            <a href="<?= h($href) ?>" class="guide-card"
               style="border-top: 3px solid <?= $col['bg'] ?>;">
                <div class="guide-card-icon"
                     style="background:<?= $col['light'] ?>;">
                    <i class="fas <?= h($icon) ?>"
                       style="color:<?= $col['bg'] ?>;"></i>
                </div>
                <div class="guide-card-body">
                    <div class="guide-card-title"><?= h($item['title']) ?></div>
                    <?php if ($item['parent']): ?>
                    <div class="guide-card-parent">
                        <i class="fas fa-folder" style="font-size:9px;margin-right:3px;"></i>
                        <?= h($item['parent']) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="guide-card-arrow" style="color:<?= $col['bg'] ?>;">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>
