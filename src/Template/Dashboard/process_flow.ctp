<?php
/**
 * System Documentation (sys-doc) — TMM Apprentice Management pipeline.
 *
 * Landing page after login: explains how the system works, stage by stage
 * (Recruitment → Training → Documentation & Departure → Apprenticeship),
 * highlights the stages relevant to the logged-in role and links directly
 * to every screen mentioned. Multi-language: Indonesian / English / Japanese.
 */
$currentLang = $this->request->getSession()->read('Config.language') ?: 'ind';
$L = $currentLang === 'eng' ? 'eng' : ($currentLang === 'jpn' ? 'jpn' : 'ind');
$userRoles = isset($userRoles) ? (array)$userRoles : [];

// which pipeline stages each role works in
$roleStages = [
    'administrator'     => ['recruitment', 'training', 'documentation', 'apprenticeship', 'stakeholders', 'accounting', 'administration'],
    'management'        => ['recruitment', 'training', 'documentation', 'apprenticeship', 'stakeholders', 'accounting'],
    'director'          => ['recruitment', 'training', 'documentation', 'apprenticeship', 'stakeholders', 'accounting'],
    'tmm-recruitment'   => ['recruitment'],
    'tmm-training'      => ['training'],
    'tmm-documentation' => ['documentation', 'apprenticeship'],
    'lpk-penyangga'     => ['recruitment'],
    'accounting'        => ['accounting'],
];
$myStages = [];
foreach ($userRoles as $r) {
    if (isset($roleStages[$r])) {
        $myStages = array_merge($myStages, $roleStages[$r]);
    }
}
$myStages = array_unique($myStages);

$t = function (array $tr) use ($L) { return $tr[$L] ?? $tr['ind']; };

// ── Pipeline definition: stage → description + screens ───────────────────────
$stages = [
    [
        'key'   => 'recruitment',
        'icon'  => 'fa-user-plus',
        'color' => '#667eea',
        'title' => ['ind' => 'Tahap 1 — Rekrutmen Kandidat (LPK)', 'eng' => 'Stage 1 — Candidate Recruitment (LPK)', 'jpn' => '第1段階 — 候補者募集（LPK）'],
        'desc'  => [
            'ind' => 'LPK penyangga mendaftarkan kandidat. Kandidat mengikuti tes fisik, penilaian (scoring), wawancara, dan medical check-up. Kandidat yang lulus dipromosikan menjadi peserta pelatihan (trainee).',
            'eng' => 'Partner LPK institutions register candidates. Candidates take physical tests, scoring, interviews and medical check-ups. Passing candidates are promoted to trainees.',
            'jpn' => '提携LPKが候補者を登録します。候補者は体力テスト、採点、面接、健康診断を受けます。合格した候補者は訓練生に昇格します。',
        ],
        'links' => [
            ['/admin/lpk-registration/create', ['ind' => 'Registrasi LPK', 'eng' => 'LPK Registration', 'jpn' => 'LPK登録']],
            ['/candidates', ['ind' => 'Data Kandidat', 'eng' => 'Candidates', 'jpn' => '候補者一覧']],
            ['/lpk-physical-tests', ['ind' => 'Tes Fisik', 'eng' => 'Physical Tests', 'jpn' => '体力テスト']],
            ['/lpk-candidate-scoring', ['ind' => 'Penilaian Kandidat', 'eng' => 'Candidate Scoring', 'jpn' => '候補者採点']],
            ['/candidate-record-interviews', ['ind' => 'Wawancara', 'eng' => 'Interviews', 'jpn' => '面接']],
            ['/candidate-record-medical-check-ups', ['ind' => 'Medical Check-Up', 'eng' => 'Medical Check-Ups', 'jpn' => '健康診断']],
            ['/candidate-documents', ['ind' => 'Dokumen Kandidat', 'eng' => 'Candidate Documents', 'jpn' => '候補者書類']],
            ['/candidates/promote-to-trainee', ['ind' => 'Promosi ke Trainee', 'eng' => 'Promote to Trainee', 'jpn' => '訓練生へ昇格']],
        ],
    ],
    [
        'key'   => 'training',
        'icon'  => 'fa-graduation-cap',
        'color' => '#2193b0',
        'title' => ['ind' => 'Tahap 2 — Pelatihan (Trainee)', 'eng' => 'Stage 2 — Training (Trainee)', 'jpn' => '第2段階 — 訓練（訓練生）'],
        'desc'  => [
            'ind' => 'Trainee mengikuti batch pelatihan: bahasa Jepang, kompetensi kerja, dan budaya. Nilai tes harian dicatat, sertifikat diterbitkan. Trainee yang lulus dipromosikan menjadi apprentice.',
            'eng' => 'Trainees join training batches: Japanese language, work competencies and culture. Daily test scores are recorded and certificates issued. Passing trainees are promoted to apprentices.',
            'jpn' => '訓練生は研修バッチに参加します：日本語、職業能力、文化。日々のテスト成績が記録され、修了証が発行されます。合格した訓練生は実習生に昇格します。',
        ],
        'links' => [
            ['/trainees', ['ind' => 'Data Trainee', 'eng' => 'Trainees', 'jpn' => '訓練生一覧']],
            ['/trainee-training-batches/index', ['ind' => 'Batch Pelatihan', 'eng' => 'Training Batches', 'jpn' => '研修バッチ']],
            ['/trainee-training-test-scores/index', ['ind' => 'Nilai Tes', 'eng' => 'Test Scores', 'jpn' => 'テスト成績']],
            ['/trainee-certificates/index', ['ind' => 'Sertifikat', 'eng' => 'Certificates', 'jpn' => '修了証']],
            ['/trainees/promotion-checklist', ['ind' => 'Checklist Promosi', 'eng' => 'Promotion Checklist', 'jpn' => '昇格チェックリスト']],
            ['/trainees/promote-to-apprentice', ['ind' => 'Promosi ke Apprentice', 'eng' => 'Promote to Apprentice', 'jpn' => '実習生へ昇格']],
        ],
    ],
    [
        'key'   => 'documentation',
        'icon'  => 'fa-passport',
        'color' => '#11998e',
        'title' => ['ind' => 'Tahap 3 — Dokumen & Keberangkatan', 'eng' => 'Stage 3 — Documents & Departure', 'jpn' => '第3段階 — 書類と出発'],
        'desc'  => [
            'ind' => 'Semua dokumen pra-keberangkatan disiapkan: dokumen pengajuan, paspor, medical check-up, COE/Visa, lalu tiket & jadwal penerbangan ke Jepang.',
            'eng' => 'All pre-departure documents are prepared: submission documents, passports, medical check-ups, COE/Visa, then tickets & flight schedules to Japan.',
            'jpn' => '出発前のすべての書類を準備します：提出書類、パスポート、健康診断、在留資格/ビザ、そして日本への航空券とフライト日程。',
        ],
        'links' => [
            ['/trainee-submission-documents/index', ['ind' => 'Dokumen Pengajuan', 'eng' => 'Submission Documents', 'jpn' => '提出書類']],
            ['/trainee-submission-documents/checklist', ['ind' => 'Checklist Dokumen', 'eng' => 'Document Checklist', 'jpn' => '書類チェックリスト']],
            ['/trainee-record-pasports', ['ind' => 'Paspor', 'eng' => 'Passports', 'jpn' => 'パスポート']],
            ['/trainee-record-medical-check-ups', ['ind' => 'Medical Check-Up', 'eng' => 'Medical Check-Ups', 'jpn' => '健康診断']],
            ['/trainee-record-coe-visas', ['ind' => 'COE / Visa', 'eng' => 'COE / Visa', 'jpn' => '在留資格／ビザ']],
            ['/tickets', ['ind' => 'Tiket', 'eng' => 'Tickets', 'jpn' => '航空券']],
            ['/tickets/departures', ['ind' => 'Keberangkatan', 'eng' => 'Departures', 'jpn' => '出発']],
            ['/trainee-documents/departure', ['ind' => 'Dokumen Keberangkatan', 'eng' => 'Departure Documents', 'jpn' => '出発書類']],
        ],
    ],
    [
        'key'   => 'apprenticeship',
        'icon'  => 'fa-industry',
        'color' => '#f7971e',
        'title' => ['ind' => 'Tahap 4 — Pemagangan di Jepang (Apprentice)', 'eng' => 'Stage 4 — Apprenticeship in Japan', 'jpn' => '第4段階 — 日本での技能実習'],
        'desc'  => [
            'ind' => 'Apprentice bekerja di organisasi penerima di Jepang sesuai pesanan (order). Dokumen, sertifikasi, dan perkembangan dicatat sampai selesai; alumni dikelola sebagai post-apprentice.',
            'eng' => 'Apprentices work at accepting organizations in Japan according to orders. Documents, certifications and progress are tracked until completion; alumni are managed as post-apprentices.',
            'jpn' => '実習生は注文に基づき日本の受入機関で働きます。書類、資格、進捗が完了まで記録され、修了者はポスト実習生として管理されます。',
        ],
        'links' => [
            ['/apprentice-orders', ['ind' => 'Order Pemagangan', 'eng' => 'Apprentice Orders', 'jpn' => '実習注文']],
            ['/apprentices/index', ['ind' => 'Data Apprentice', 'eng' => 'Apprentices', 'jpn' => '実習生一覧']],
            ['/apprentice-documents/index', ['ind' => 'Dokumen Apprentice', 'eng' => 'Apprentice Documents', 'jpn' => '実習生書類']],
            ['/apprentice-orders/statistics', ['ind' => 'Statistik Order', 'eng' => 'Order Statistics', 'jpn' => '注文統計']],
            ['/post-apprentices/index', ['ind' => 'Post-Apprentice', 'eng' => 'Post-Apprentices', 'jpn' => 'ポスト実習生']],
        ],
    ],
    [
        'key'   => 'stakeholders',
        'icon'  => 'fa-building',
        'color' => '#8360c3',
        'title' => ['ind' => 'Pendukung — Pemangku Kepentingan', 'eng' => 'Supporting — Stakeholders', 'jpn' => 'サポート — 関係機関'],
        'desc'  => [
            'ind' => 'Data organisasi yang terlibat: LPK penyangga, organisasi penerima di Jepang, asosiasi kerjasama, dan lembaga pendukung skill khusus.',
            'eng' => 'Organizations involved: partner LPKs, accepting organizations in Japan, cooperative associations and special skill support institutions.',
            'jpn' => '関係する組織：提携LPK、日本の受入機関、協同組合、特定技能支援機関。',
        ],
        'links' => [
            ['/vocational-training-institutions', ['ind' => 'LPK Penyangga', 'eng' => 'LPK Institutions', 'jpn' => 'LPK機関']],
            ['/acceptance-organizations', ['ind' => 'Organisasi Penerima', 'eng' => 'Acceptance Organizations', 'jpn' => '受入機関']],
            ['/cooperative-associations', ['ind' => 'Asosiasi Kerjasama', 'eng' => 'Cooperative Associations', 'jpn' => '協同組合']],
            ['/special-skill-support-institutions', ['ind' => 'Lembaga Skill Khusus', 'eng' => 'Support Institutions', 'jpn' => '支援機関']],
        ],
    ],
    [
        'key'   => 'accounting',
        'icon'  => 'fa-coins',
        'color' => '#38ef7d',
        'title' => ['ind' => 'Pendukung — Keuangan', 'eng' => 'Supporting — Accounting', 'jpn' => 'サポート — 会計'],
        'desc'  => [
            'ind' => 'Cicilan pembayaran trainee, jurnal, bagan akun, dan laporan keuangan (neraca, laba rugi, arus kas).',
            'eng' => 'Trainee installments, journals, chart of accounts and financial reports (balance sheet, income statement, cash flow).',
            'jpn' => '訓練生の分割払い、仕訳帳、勘定科目表、財務報告書（貸借対照表、損益計算書、キャッシュフロー）。',
        ],
        'links' => [
            ['/trainee-installments', ['ind' => 'Cicilan Trainee', 'eng' => 'Installments', 'jpn' => '分割払い']],
            ['/journals', ['ind' => 'Jurnal', 'eng' => 'Journals', 'jpn' => '仕訳帳']],
            ['/chart-of-accounts', ['ind' => 'Bagan Akun', 'eng' => 'Chart of Accounts', 'jpn' => '勘定科目表']],
            ['/reports/balance-sheet', ['ind' => 'Neraca', 'eng' => 'Balance Sheet', 'jpn' => '貸借対照表']],
            ['/reports/income-statement', ['ind' => 'Laba Rugi', 'eng' => 'Income Statement', 'jpn' => '損益計算書']],
            ['/reports/cash-flow', ['ind' => 'Arus Kas', 'eng' => 'Cash Flow', 'jpn' => 'キャッシュフロー']],
        ],
    ],
    [
        'key'   => 'administration',
        'icon'  => 'fa-users-cog',
        'color' => '#616161',
        'title' => ['ind' => 'Pendukung — Administrasi Sistem', 'eng' => 'Supporting — System Administration', 'jpn' => 'サポート — システム管理'],
        'desc'  => [
            'ind' => 'Pengelolaan pengguna, peran (role), menu, izin akses, dan log audit sistem.',
            'eng' => 'Management of users, roles, menus, permissions and system audit logs.',
            'jpn' => 'ユーザー、ロール、メニュー、権限、監査ログの管理。',
        ],
        'links' => [
            ['/users', ['ind' => 'Pengguna', 'eng' => 'Users', 'jpn' => 'ユーザー']],
            ['/roles', ['ind' => 'Peran', 'eng' => 'Roles', 'jpn' => 'ロール']],
            ['/menus', ['ind' => 'Menu', 'eng' => 'Menus', 'jpn' => 'メニュー']],
            ['/permissions', ['ind' => 'Izin Akses', 'eng' => 'Permissions', 'jpn' => '権限']],
            ['/audit', ['ind' => 'Log Audit', 'eng' => 'Audit Logs', 'jpn' => '監査ログ']],
        ],
    ],
];
?>

<style>
.content-wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
.sysdoc-header {
    text-align: center; padding: 28px 20px; margin-bottom: 24px; border-radius: 15px; color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.25);
}
.sysdoc-header h1 { margin: 0 0 8px; font-size: 26px; }
.sysdoc-header p { margin: 0; opacity: .9; }
.sysdoc-actions { margin-top: 16px; }
.sysdoc-actions a {
    display: inline-block; margin: 4px 6px; padding: 10px 22px; border-radius: 25px;
    background: rgba(255,255,255,.18); color: #fff; text-decoration: none; font-weight: 600;
    border: 1px solid rgba(255,255,255,.5); transition: all .2s;
}
.sysdoc-actions a:hover { background: #fff; color: #667eea; }
.language-switcher { text-align: center; margin-bottom: 24px; }
.lang-btn {
    display: inline-block; padding: 8px 22px; margin: 0 5px; border-radius: 25px; text-decoration: none;
    background: #f0f1f5; color: #444; font-weight: 600; transition: all .2s;
}
.lang-btn.active, .lang-btn:hover { background: #667eea; color: #fff; text-decoration: none; }
.flow-section {
    background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
}
.flow-section h2 { margin: 0 0 14px; font-size: 20px; color: #333; }
.stage-card {
    border: 1px solid #e5e7eb; border-left: 6px solid #999; border-radius: 10px;
    padding: 18px 20px; margin-bottom: 16px; background: #fafbfc; position: relative;
}
.stage-card.my-stage { background: #f5f8ff; border-color: #c7d2fe; box-shadow: 0 2px 10px rgba(102,126,234,.15); }
.stage-title { font-size: 17px; font-weight: 700; color: #333; margin-bottom: 6px; }
.stage-title i { margin-right: 8px; }
.stage-desc { color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 12px; }
.role-badge {
    position: absolute; top: 14px; right: 16px; background: #667eea; color: #fff;
    padding: 3px 12px; border-radius: 12px; font-size: 11px; font-weight: 700;
}
.stage-links { display: flex; flex-wrap: wrap; gap: 8px; }
.stage-links a {
    display: inline-block; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 600;
    background: #fff; border: 1px solid #d1d5db; color: #374151; text-decoration: none; transition: all .15s;
}
.stage-links a:hover { border-color: #667eea; color: #667eea; text-decoration: none; }
.stage-links a i { margin-right: 5px; font-size: 11px; }
.mermaid { text-align: center; background: #fafbfc; border-radius: 10px; padding: 12px; }
@media (max-width: 768px) { .stage-links { flex-direction: column; } }
</style>

<div class="content-wrapper">

    <!-- Header -->
    <div class="sysdoc-header">
        <h1><i class="fas fa-sitemap"></i>
            <?= $t(['ind' => 'Dokumentasi Sistem TMM', 'eng' => 'TMM System Documentation', 'jpn' => 'TMMシステムドキュメント']) ?>
        </h1>
        <p>
            <?= $t([
                'ind' => 'Bagaimana sistem bekerja mengikuti alur proses: Rekrutmen → Pelatihan → Dokumen & Keberangkatan → Pemagangan di Jepang',
                'eng' => 'How the system works following the process pipeline: Recruitment → Training → Documents & Departure → Apprenticeship in Japan',
                'jpn' => 'プロセスの流れに沿ったシステムの仕組み：募集 → 訓練 → 書類と出発 → 日本での技能実習',
            ]) ?>
        </p>
        <div class="sysdoc-actions">
            <a href="/"><i class="fas fa-tachometer-alt"></i>
                <?= $t(['ind' => 'Buka Dashboard Saya', 'eng' => 'Open My Dashboard', 'jpn' => 'マイダッシュボードを開く']) ?>
            </a>
        </div>
    </div>

    <!-- Language Switcher -->
    <div class="language-switcher">
        <a href="?lang=ind" class="lang-btn <?= $L === 'ind' ? 'active' : '' ?>">🇮🇩 Indonesia</a>
        <a href="?lang=eng" class="lang-btn <?= $L === 'eng' ? 'active' : '' ?>">🇬🇧 English</a>
        <a href="?lang=jpn" class="lang-btn <?= $L === 'jpn' ? 'active' : '' ?>">🇯🇵 日本語</a>
    </div>

    <!-- Pipeline overview diagram -->
    <div class="flow-section">
        <h2><i class="fas fa-project-diagram"></i>
            <?= $t(['ind' => 'Alur Proses Utama', 'eng' => 'Main Process Pipeline', 'jpn' => 'メインプロセスフロー']) ?>
        </h2>
        <div class="mermaid">
graph LR
    A["<?= $t(['ind' => '1. Rekrutmen Kandidat', 'eng' => '1. Candidate Recruitment', 'jpn' => '1. 候補者募集']) ?>"] --> B["<?= $t(['ind' => '2. Pelatihan Trainee', 'eng' => '2. Trainee Training', 'jpn' => '2. 訓練生の訓練']) ?>"]
    B --> C["<?= $t(['ind' => '3. Dokumen & Keberangkatan', 'eng' => '3. Documents & Departure', 'jpn' => '3. 書類と出発']) ?>"]
    C --> D["<?= $t(['ind' => '4. Pemagangan di Jepang', 'eng' => '4. Apprenticeship in Japan', 'jpn' => '4. 日本での技能実習']) ?>"]
    D --> E["<?= $t(['ind' => 'Post-Apprentice', 'eng' => 'Post-Apprentice', 'jpn' => 'ポスト実習生']) ?>"]
    S["<?= $t(['ind' => 'Pemangku Kepentingan', 'eng' => 'Stakeholders', 'jpn' => '関係機関']) ?>"] -.-> A
    S -.-> D
    K["<?= $t(['ind' => 'Keuangan', 'eng' => 'Accounting', 'jpn' => '会計']) ?>"] -.-> B
    K -.-> C
    style A fill:#e8ecfd,stroke:#667eea
    style B fill:#e0f2f7,stroke:#2193b0
    style C fill:#e0f5f1,stroke:#11998e
    style D fill:#fdf0dd,stroke:#f7971e
    style E fill:#eeeeee,stroke:#616161
        </div>
    </div>

    <!-- Stage cards with jump links -->
    <div class="flow-section">
        <h2><i class="fas fa-list-ol"></i>
            <?= $t(['ind' => 'Penjelasan Tiap Tahap & Halaman Terkait', 'eng' => 'Each Stage Explained & Related Pages', 'jpn' => '各段階の説明と関連ページ']) ?>
        </h2>

        <?php foreach ($stages as $stage): ?>
            <?php $mine = in_array($stage['key'], $myStages); ?>
            <div class="stage-card <?= $mine ? 'my-stage' : '' ?>" style="border-left-color: <?= $stage['color'] ?>;">
                <?php if ($mine): ?>
                    <span class="role-badge"><i class="fas fa-user-check"></i>
                        <?= $t(['ind' => 'Peran Anda', 'eng' => 'Your Role', 'jpn' => 'あなたの役割']) ?>
                    </span>
                <?php endif; ?>
                <div class="stage-title" style="color: <?= $stage['color'] ?>;">
                    <i class="fas <?= $stage['icon'] ?>"></i><?= h($t($stage['title'])) ?>
                </div>
                <div class="stage-desc"><?= h($t($stage['desc'])) ?></div>
                <div class="stage-links">
                    <?php foreach ($stage['links'] as $link): ?>
                        <a href="<?= h($link[0]) ?>"><i class="fas fa-arrow-right"></i><?= h($t($link[1])) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- How to use -->
    <div class="flow-section">
        <h2><i class="fas fa-question-circle"></i>
            <?= $t(['ind' => 'Cara Menggunakan Sistem', 'eng' => 'How to Use the System', 'jpn' => 'システムの使い方']) ?>
        </h2>
        <ul style="color:#555; line-height:1.9;">
            <li><?= $t([
                'ind' => 'Gunakan menu di bagian atas layar untuk membuka setiap modul, atau klik tautan pada tahap di atas.',
                'eng' => 'Use the menu at the top of the screen to open each module, or click the links on the stages above.',
                'jpn' => '画面上部のメニューから各モジュールを開くか、上記の各段階のリンクをクリックしてください。',
            ]) ?></li>
            <li><?= $t([
                'ind' => 'Setiap halaman daftar (index) memiliki filter kolom, pencarian, dan tombol ekspor.',
                'eng' => 'Every list (index) page has column filters, search and export buttons.',
                'jpn' => '各一覧ページには列フィルター、検索、エクスポートボタンがあります。',
            ]) ?></li>
            <li><?= $t([
                'ind' => 'Tombol bantuan "Alur Proses" pada tiap modul menjelaskan alur kerja modul tersebut secara detail.',
                'eng' => 'The "Process Flow" help button on each module explains that module\'s workflow in detail.',
                'jpn' => '各モジュールの「プロセスフロー」ヘルプボタンで、そのモジュールのワークフローの詳細を確認できます。',
            ]) ?></li>
            <li><?= $t([
                'ind' => 'Akses menu dan tombol menyesuaikan peran (role) akun Anda.',
                'eng' => 'Menu access and buttons follow your account\'s role.',
                'jpn' => 'メニューやボタンはアカウントのロールに応じて表示されます。',
            ]) ?></li>
        </ul>
    </div>

</div><!-- End .content-wrapper -->

<?php if ($this->layout !== 'process_flow'): ?>
<!-- Mermaid is provided by the standalone process_flow layout; when rendered
     inside the main app layout (menu visible) we load it here instead. -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
if (window.mermaid) {
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        themeVariables: {
            primaryColor: '#667eea'
        }
    });
}
</script>
<?php endif; ?>
