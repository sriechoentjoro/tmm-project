<?php
/**
 * One trainee's scores over time.
 *
 * The list screen holds every score of every trainee as one row each, so
 * whether somebody is getting better is not a question it can answer: the
 * grade column says A or B per row and nothing across rows. Here the same
 * rows are plotted in the order they were sat, a line per competency, with
 * the grade bands behind them.
 *
 * @var \App\View\AppView $this
 * @var array $trainees Everyone who has sat something, for the picker.
 * @var array|null $trainee The one being shown.
 * @var int|null $traineeId
 * @var array $rows Their scores, oldest first.
 * @var array $series The same rows grouped by competency.
 * @var array|null $summary Totals, or null when there is nothing to total.
 * @var array $bands The grade bands.
 */

// The same grade colours the list screen uses, so a B is the same blue here.
$gradeStyle = [
    'A' => '#2E7D32',
    'B' => '#1565C0',
    'C' => '#F57F17',
    'D' => '#E65100',
    'E' => '#B71C1C',
];

// One colour per competency line. Enough for the competencies this module
// has; it wraps rather than running out.
$lineColours = ['#0288D1', '#6949BC', '#00897B', '#E65100', '#C2185B',
    '#5D4037', '#455A64', '#F9A825'];
?>
<style>
.progress-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; flex-wrap: wrap; gap: 10px;
}
.progress-header h2 {
    margin: 0; font-size: 20px; font-weight: 700; color: #1a202c;
    display: flex; align-items: center; gap: 10px;
}
.progress-header h2 i { color: #0288D1; font-size: 18px; }
.progress-card {
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    padding: 16px; margin-bottom: 18px;
}
.progress-pick { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.progress-pick label {
    font-size: 12px; font-weight: 600; color: #64748b;
    display: block; margin-bottom: 4px;
}
.progress-pick select {
    min-width: 280px; padding: 7px 10px; border: 1.5px solid #cbd5e1;
    border-radius: 8px; font-size: 14px;
}
.progress-stats { display: flex; gap: 12px; flex-wrap: wrap; }
.progress-stat {
    flex: 1 1 120px; background: #f8fafc; border-radius: 9px;
    padding: 10px 14px; border: 1px solid #e2e8f0;
}
.progress-stat .label {
    font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.03em;
}
.progress-stat .value { font-size: 22px; font-weight: 700; color: #1a202c; }
.progress-stat .value.up { color: #2E7D32; }
.progress-stat .value.down { color: #B71C1C; }
.progress-stat .value.flat { color: #64748b; }
.progress-chart { position: relative; height: 340px; }
.progress-small { position: relative; height: 190px; }
.progress-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 14px;
}
.progress-sub {
    font-size: 13px; font-weight: 700; color: #334155;
    margin: 0 0 8px; display: flex; justify-content: space-between; gap: 8px;
}
.progress-sub .sub-note { font-weight: 600; color: #64748b; font-size: 12px; }
.progress-empty {
    padding: 28px; text-align: center; color: #64748b; font-size: 14px;
}
.progress-bands { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
.progress-bands span {
    font-size: 11px; font-weight: 600; padding: 3px 9px;
    border-radius: 999px; border: 1.5px solid; background: #fff;
}
</style>

<div class="progress-header">
    <h2>
        <i class="fas fa-chart-line"></i>
        <?= __('Trainee Progress') ?>
    </h2>
    <?= $this->Html->link(
        '<i class="fas fa-arrow-left"></i> ' . __('Back to Scores'),
        ['action' => 'index'],
        ['class' => 'cross-link', 'escape' => false, 'style' => 'color:#0288D1;border:1.5px solid #0288D1;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;']
    ) ?>
</div>

<div class="progress-card">
    <?= $this->Form->create(null, ['type' => 'get', 'class' => 'progress-pick']) ?>
        <div>
            <label for="trainee-pick"><?= __('Trainee') ?></label>
            <select name="trainee_id" id="trainee-pick" onchange="this.form.submit()">
                <?php foreach ($trainees as $one): ?>
                    <option value="<?= h($one['id']) ?>"
                        <?= (int)$one['id'] === (int)$traineeId ? 'selected' : '' ?>>
                        <?= h($one['name']) ?><?= $one['tmm_code'] ? ' (' . h($one['tmm_code']) . ')' : '' ?>
                        &mdash; <?= $this->Number->format($one['tests']) ?> <?= __('tests') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <noscript>
            <button type="submit" class="btn btn-primary"><?= __('Show') ?></button>
        </noscript>
    <?= $this->Form->end() ?>
    <?php if ($bands): ?>
        <div class="progress-bands">
            <?php foreach ($bands as $band): ?>
                <?php $colour = isset($gradeStyle[$band['title']]) ? $gradeStyle[$band['title']] : '#64748b'; ?>
                <span style="color:<?= h($colour) ?>;border-color:<?= h($colour) ?>;">
                    <?= h($band['title']) ?> &middot; <?= h($band['min_score']) ?>&ndash;<?= h($band['max_score']) ?>
                    <?= $band['description'] ? ' · ' . h($band['description']) : '' ?>
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if (!$rows): ?>
    <div class="progress-card progress-empty">
        <i class="fas fa-info-circle"></i>
        <?= $trainees
            ? __('This trainee has no scores recorded yet.')
            : __('No scores have been recorded for anybody yet.') ?>
    </div>
<?php else: ?>

<div class="progress-card">
    <div class="progress-stats">
        <div class="progress-stat">
            <div class="label"><?= __('Tests') ?></div>
            <div class="value"><?= $this->Number->format($summary['tests']) ?></div>
        </div>
        <div class="progress-stat">
            <div class="label"><?= __('Competencies') ?></div>
            <div class="value"><?= $this->Number->format($summary['competencies']) ?></div>
        </div>
        <div class="progress-stat">
            <div class="label"><?= __('Average') ?></div>
            <div class="value"><?= h($summary['average']) ?></div>
        </div>
        <div class="progress-stat">
            <div class="label"><?= __('Lowest') ?> &ndash; <?= __('Highest') ?></div>
            <div class="value"><?= h($summary['lowest']) ?>&ndash;<?= h($summary['highest']) ?></div>
        </div>
        <div class="progress-stat">
            <div class="label"><?= __('First to latest') ?></div>
            <?php
            // The number people actually came for, stated rather than left to
            // be worked out from the shape of a line.
            $change = (int)$summary['change'];
            $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
            ?>
            <div class="value <?= $direction ?>">
                <?= $change > 0 ? '+' : '' ?><?= h($change) ?>
            </div>
            <div class="label" style="text-transform:none;letter-spacing:0;">
                <?= h($summary['first']) ?> &rarr; <?= h($summary['latest']) ?>
            </div>
        </div>
    </div>
</div>

<div class="progress-card">
    <div class="progress-sub">
        <span><?= __('Every test, in the order it was taken') ?></span>
        <span class="sub-note"><?= h($summary['from']) ?> &ndash; <?= h($summary['to']) ?></span>
    </div>
    <div class="progress-chart"><canvas id="progressAll"></canvas></div>
</div>

<?php if (count($series) > 1): ?>
<div class="progress-grid">
    <?php $n = 0; foreach ($series as $name => $points): ?>
        <?php
        // Each competency's own figures, from the points already in hand: the
        // card says how this one went without anybody reading the line.
        $theseScores = array_map(function ($point) {
            return $point['score'];
        }, $points);
        $thisAverage = round(array_sum($theseScores) / count($theseScores), 1);
        $latestGrade = $points[count($points) - 1]['grade'];
        $latestColour = isset($gradeStyle[$latestGrade])
            ? $gradeStyle[$latestGrade] : '#64748b';
        ?>
        <div class="progress-card">
            <div class="progress-sub">
                <span><?= h($name) ?></span>
                <span class="sub-note">
                    <?= count($points) ?> <?= __('tests') ?>
                    &middot; <?= __('Average') ?> <?= h($thisAverage) ?>
                    <?php if ($latestGrade): ?>
                        <span style="display:inline-block;margin-left:4px;padding:1px 7px;border-radius:999px;border:1.5px solid <?= h($latestColour) ?>;color:<?= h($latestColour) ?>;">
                            <?= h($latestGrade) ?>
                        </span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="progress-small">
                <canvas id="progressSeries<?= $n ?>"></canvas>
            </div>
        </div>
    <?php $n++; endforeach; ?>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
(function () {
    var COLOURS = <?= json_encode(array_values($lineColours)) ?>;
    var SERIES = <?= json_encode(array_map(function ($points) {
        return $points;
    }, $series), JSON_UNESCAPED_UNICODE) ?>;
    var NAMES = <?= json_encode(array_keys($series), JSON_UNESCAPED_UNICODE) ?>;
    var BANDS = <?= json_encode(array_map(function ($band) use ($gradeStyle) {
        return [
            'title' => $band['title'],
            'min' => (int)$band['min_score'],
            'max' => (int)$band['max_score'],
            'colour' => isset($gradeStyle[$band['title']])
                ? $gradeStyle[$band['title']] : '#64748b',
        ];
    }, $bands), JSON_UNESCAPED_UNICODE) ?>;

    /**
     * The grade behind a score, so a point can be coloured by what it means.
     *
     * The bands come from master_training_test_score_grades, not from numbers
     * written here: this application already decides A at 81 and B at 61 in
     * one place, and a chart that disagrees with the table beside it is worse
     * than no chart.
     */
    function bandOf(score) {
        for (var i = 0; i < BANDS.length; i++) {
            if (score >= BANDS[i].min && score <= BANDS[i].max) {
                return BANDS[i];
            }
        }
        return null;
    }

    function colourOf(score) {
        var band = bandOf(score);
        return band ? band.colour : '#64748b';
    }

    /**
     * Paint the grade bands behind the plot.
     *
     * The request was that the predicate be the chart, not a column beside it.
     * A line at 78 says little; a line sitting in the band marked B, just
     * under the one marked A, says what everybody actually wants to know.
     *
     * Drawn as a plugin rather than with chartjs-plugin-annotation, which
     * would be a second library from a second CDN for fifteen lines of work.
     */
    var gradeBands = {
        id: 'gradeBands',
        beforeDatasetsDraw: function (chart) {
            var y = chart.scales.y;
            var area = chart.chartArea;
            if (!y || !area) {
                return;
            }
            var ctx = chart.ctx;
            ctx.save();
            BANDS.forEach(function (band) {
                var top = y.getPixelForValue(Math.min(band.max, y.max));
                var bottom = y.getPixelForValue(Math.max(band.min, y.min));
                if (bottom <= top) {
                    return;
                }
                ctx.fillStyle = band.colour;
                ctx.globalAlpha = 0.07;
                ctx.fillRect(area.left, top, area.right - area.left, bottom - top);
                ctx.globalAlpha = 1;
                ctx.fillStyle = band.colour;
                ctx.font = '600 11px sans-serif';
                ctx.textAlign = 'left';
                ctx.textBaseline = 'top';
                ctx.fillText(band.title, area.left + 4, top + 2);
            });
            ctx.restore();
        }
    };

    // The y axis is fixed to 0-100 on every chart. Letting Chart.js choose it
    // per competency makes a 78 and an 82 look like a cliff on one card and a
    // flat line on the next, which is the opposite of what a comparison is for.
    var SCALE = {
        y: { min: 0, max: 100, ticks: { stepSize: 20 } },
        x: { ticks: { maxRotation: 0, autoSkip: true } }
    };

    function labelsOf(points) {
        return points.map(function (p) { return p.date; });
    }

    function valuesOf(points) {
        return points.map(function (p) { return p.score; });
    }

    function tooltipGrade(points) {
        return {
            callbacks: {
                afterLabel: function (ctx) {
                    var p = points[ctx.dataIndex];
                    return p && p.grade ? '<?= __('Grade') ?>: ' + p.grade : '';
                }
            }
        };
    }

    var all = document.getElementById('progressAll');
    if (all && window.Chart) {
        new Chart(all.getContext('2d'), {
            type: 'line',
            data: {
                datasets: NAMES.map(function (name, i) {
                    var points = SERIES[name];
                    return {
                        label: name,
                        data: points.map(function (p) {
                            return { x: p.date, y: p.score };
                        }),
                        borderColor: COLOURS[i % COLOURS.length],
                        backgroundColor: COLOURS[i % COLOURS.length],
                        pointBackgroundColor: points.map(function (p) {
                            return colourOf(p.score);
                        }),
                        pointBorderColor: points.map(function (p) {
                            return colourOf(p.score);
                        }),
                        tension: 0.25,
                        pointRadius: 4
                    };
                })
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                parsing: false,
                scales: {
                    y: SCALE.y,
                    x: { type: 'category', labels: <?= json_encode(array_values(array_unique(array_map(function ($row) {
                        return $row['test_date'];
                    }, $rows)))) ?> }
                },
                plugins: { legend: { position: 'bottom' } }
            },
            plugins: [gradeBands]
        });
    }

    NAMES.forEach(function (name, i) {
        var el = document.getElementById('progressSeries' + i);
        if (!el || !window.Chart) {
            return;
        }
        var points = SERIES[name];
        new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsOf(points),
                datasets: [{
                    label: name,
                    data: valuesOf(points),
                    borderColor: COLOURS[i % COLOURS.length],
                    backgroundColor: COLOURS[i % COLOURS.length],
                    pointBackgroundColor: points.map(function (p) {
                        return colourOf(p.score);
                    }),
                    pointBorderColor: points.map(function (p) {
                        return colourOf(p.score);
                    }),
                    pointRadius: 5,
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: SCALE,
                plugins: {
                    legend: { display: false },
                    tooltip: tooltipGrade(points)
                }
            },
            plugins: [gradeBands]
        });
    });
})();
</script>
<?php endif; ?>
