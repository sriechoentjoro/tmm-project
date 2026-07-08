<?php
/**
 * Generator: writes module-specific process_flow.ctp for all modules.
 * Run: php gen_pf.php
 */

$base = __DIR__ . '/src/Template';

// [dir, title_ind, title_eng, title_jpn, desc_ind, desc_eng, desc_jpn, steps_eng, mermaid_eng]
$modules = [
// --- CANDIDATE ---
['Candidates','Manajemen Kandidat','Candidate Management','候補者管理',
 'Halaman utama menampilkan daftar semua calon peserta magang Jepang. Data mencakup nama, foto, tanggal lahir, alamat, data fisik (tinggi, berat, golongan darah), kontak, serta status kelulusan seleksi.',
 'The main page lists all applicants for the Japan apprenticeship program. It shows name, photo, birth date, address, physical measurements, contact info, social media links, and selection pass/fail status.',
 'メインページは日本研修プログラムへの全応募者一覧を表示します。氏名、写真、生年月日、住所、身体測定値、連絡先、SNSリンク、選考合否状況を管理します。',
 ['Open Candidates list to see all applicants','Use filter/search by name, gender, region, or status','Click a candidate to view full profile with related tabs (education, family, documents, interview records)','Use Add New to register a new candidate','Change status (pass/fail) after interview or medical checkup'],
 "A[Candidate Registered] --> B[Profile Completed]\n    B --> C[Documents Submitted]\n    C --> D[Medical Checkup]\n    D --> E{Interview}\n    E -->|Pass| F[Promoted to Trainee]\n    E -->|Fail| G[Rejected / On Hold]"],

['CandidateCertifications','Sertifikasi Kandidat','Candidate Certifications','候補者資格',
 'Mencatat sertifikat dan lisensi yang dimiliki oleh kandidat sebelum program magang, seperti SIM, sertifikat keahlian, atau sertifikat bahasa.',
 'Records certifications and licenses held by each candidate prior to the apprenticeship program, such as driving licenses, skill certificates, or language certificates.',
 '研修プログラム開始前に各候補者が保有する資格・免許（運転免許、技能証明書、語学証明書など）を記録します。',
 ['Open list to see all candidate certifications','Filter by candidate or certificate type','Add certification record linked to a specific candidate','Upload certificate scan/image','Review during candidate evaluation process'],
 "A[Candidate Selected] --> B[List Existing Certificates]\n    B --> C[Enter Certificate Details]\n    C --> D[Upload Scan]\n    D --> E[Linked to Candidate Profile]"],

['CandidateCourses','Kursus Kandidat','Candidate Courses','候補者コース',
 'Mencatat kursus atau pelatihan yang pernah diikuti kandidat, seperti kursus bahasa Jepang, kursus komputer, atau pelatihan kerja sebelum mendaftar program magang.',
 'Records training courses and classes attended by candidates before joining the program, such as Japanese language courses, computer courses, or job skills training.',
 '研修プログラム参加前に候補者が受講したコース（日本語コース、パソコン講座、職業訓練など）を記録します。',
 ['View list of all courses per candidate','Add course record with institution, duration, and result','Filter by course type or candidate','Link courses to candidate profile for evaluation','Use during selection to assess candidate readiness'],
 "A[Candidate Registered] --> B[List Prior Courses]\n    B --> C[Enter Course Details]\n    C --> D[Record Completion/Score]\n    D --> E[Used in Evaluation]"],

['CandidateDocumentCategories','Kategori Dokumen Kandidat','Candidate Document Categories','候補者書類カテゴリー',
 'Mengelola kategori dokumen yang harus dilengkapi oleh kandidat, seperti KTP, ijazah, foto, SKCK, dan dokumen medis. Halaman ini adalah referensi master untuk sistem manajemen dokumen kandidat.',
 'Manages the master list of document categories required from candidates, such as national ID, diploma, photo, police clearance, and medical documents. This is the reference table for the candidate document management system.',
 '候補者に必要な書類カテゴリ（身分証明書、卒業証書、写真、警察証明書、医療書類など）のマスターリストを管理します。',
 ['View all document category types','Add a new document category with name and requirements','Mark categories as mandatory or optional','Set per-category validation rules','Categories are used in candidate document tracking'],
 "A[Define Category] --> B[Set Required/Optional]\n    B --> C[Assign to Candidate Flow]\n    C --> D[Candidates Submit Documents]\n    D --> E[Tracked per Category]"],

['CandidateDocuments','Dokumen Kandidat','Candidate Documents','候補者書類',
 'Melacak status pengumpulan dokumen per kandidat. Setiap baris menunjukkan kandidat, jenis dokumen, status (belum/sudah dikumpulkan), dan file yang diunggah.',
 'Tracks document submission status per candidate. Each record shows the candidate, document type, submission status (pending/submitted), and uploaded file.',
 '候補者ごとの書類提出状況を追跡します。各レコードは候補者、書類種別、提出状況（未提出/提出済み）、アップロードファイルを表示します。',
 ['View all document submission records','Filter by candidate, document type, or status','Upload a scanned document for a candidate','Update status when physical document is received','Generate completeness report before candidate moves to next stage'],
 "A[Document Required] --> B[Candidate Uploads File]\n    B --> C[Staff Verifies]\n    C --> D{Complete?}\n    D -->|Yes| E[Marked Submitted]\n    D -->|No| F[Follow Up Required]"],

['CandidateDocumentManagementDashboardDetails','Detail Dashboard Dokumen Kandidat','Candidate Document Dashboard Details','候補者書類ダッシュボード詳細',
 'Tampilan dashboard detail yang menunjukkan kelengkapan dokumen setiap kandidat. Membantu staf melacak dokumen mana yang masih kurang untuk setiap calon peserta.',
 'A detailed dashboard view showing document completeness per candidate. Helps staff quickly identify which documents are still missing for each applicant.',
 '候補者ごとの書類完備状況を示す詳細ダッシュボード。各応募者に不足している書類をスタッフが迅速に把握できます。',
 ['Open dashboard to see completeness summary per candidate','Click a candidate row to view individual document status','Filter by LPK, batch, or completion percentage','Flag incomplete candidates for follow-up','Use before submission deadline to ensure all docs are ready'],
 "A[Batch Selected] --> B[Load Candidates]\n    B --> C[Check Each Document]\n    C --> D{All Complete?}\n    D -->|Yes| E[Ready for Submission]\n    D -->|No| F[Show Missing Docs List]"],

['CandidateDocumentsMasterList','Master Daftar Dokumen Kandidat','Candidate Documents Master List','候補者書類マスターリスト',
 'Daftar master seluruh dokumen yang diperlukan dalam proses seleksi kandidat magang. Digunakan sebagai referensi untuk memastikan tidak ada dokumen yang terlewat.',
 'Master list of all documents required in the candidate selection process. Used as a reference checklist to ensure no document is missed before moving a candidate forward.',
 '研修生選抜プロセスで必要な全書類のマスターリスト。候補者を次の段階に進める前に書類の漏れがないよう確認するためのチェックリストとして使用します。',
 ['View all required document types in the master list','Add or update document entries','Assign categories to each document','Mark as mandatory/optional','Referenced by candidate document tracking system'],
 "A[Define Master Document] --> B[Assign Category]\n    B --> C[Set Mandatory Flag]\n    C --> D[Published to Candidate Checklist]\n    D --> E[Staff Tracks Compliance]"],

['CandidateEducations','Pendidikan Kandidat','Candidate Educations','候補者学歴',
 'Mencatat riwayat pendidikan formal setiap kandidat: sekolah/universitas, jurusan, tahun masuk dan lulus, serta nilai atau gelar yang diperoleh.',
 'Records the formal educational history of each candidate: institution name, major/field of study, enrollment and graduation year, and grades or qualifications obtained.',
 '各候補者の学歴を記録します：学校・大学名、専攻、入学・卒業年、取得した成績や資格。',
 ['View education records for all candidates','Add new education entry linked to a candidate','Record institution, level (SD/SMP/SMA/S1), years, and grades','Filter by education level or graduation year','Used during evaluation to verify academic background'],
 "A[Candidate Registered] --> B[Enter Education History]\n    B --> C[Verify with Documents]\n    C --> D[Staff Evaluates Qualification]\n    D --> E[Linked to Candidate Profile]"],

['CandidateExperiences','Pengalaman Kerja Kandidat','Candidate Work Experiences','候補者職歴',
 'Mencatat riwayat pengalaman kerja setiap kandidat sebelum mendaftar program magang, termasuk nama perusahaan, posisi, periode kerja, dan deskripsi pekerjaan.',
 'Records the work history of each candidate before applying to the apprenticeship program, including company name, position, employment period, and job description.',
 '研修プログラム応募前の各候補者の職歴（会社名、役職、在職期間、職務内容）を記録します。',
 ['View all work experience entries per candidate','Add work experience with company, position, and dates','Record monthly salary for context','Filter by industry or job type','Reviewed during candidate evaluation and matching with acceptance organizations'],
 "A[Candidate Registers] --> B[Enter Work History]\n    B --> C[Staff Reviews Experience]\n    C --> D[Match with Job Requirements]\n    D --> E[Influences Placement Decision]"],

['CandidateFamilies','Data Keluarga Kandidat','Candidate Families','候補者家族情報',
 'Mencatat data anggota keluarga kandidat (orang tua, saudara, pasangan) termasuk nama, pekerjaan, dan hubungan keluarga. Diperlukan untuk administrasi dan keperluan darurat.',
 'Records family member data for each candidate (parents, siblings, spouse) including name, occupation, and relationship. Required for administration and emergency contacts.',
 '各候補者の家族情報（両親、兄弟姉妹、配偶者）の氏名、職業、続柄を記録します。管理業務と緊急連絡用として必要です。',
 ['View family records per candidate','Add family member with name, relationship, occupation, contact','Link family to specific candidate','Update in case of changes (marriage, new contact)','Used for emergency contact and visa application forms'],
 "A[Candidate Registered] --> B[Enter Family Data]\n    B --> C[Verify Relationships]\n    C --> D[Stored in Profile]\n    D --> E[Used for Visa & Emergency Contact]"],

['CandidateRecordInterviews','Rekam Wawancara Kandidat','Candidate Interview Records','候補者面接記録',
 'Mencatat hasil wawancara seleksi setiap kandidat: tanggal wawancara, pewawancara, hasil (lulus/tidak), skor, dan catatan. Halaman ini adalah riwayat proses seleksi kandidat.',
 'Records the selection interview results for each candidate: interview date, interviewer, result (pass/fail), score, and notes. This page is the history of the candidate selection process.',
 '各候補者の選考面接結果（面接日、面接官、合否、スコア、メモ）を記録します。候補者選考プロセスの履歴ページです。',
 ['View all interview records with date and result','Add interview record after conducting an interview','Record score, interviewer name, and outcome','Filter by result (pass/fail) or date range','Passed candidates are promoted to trainee stage'],
 "A[Interview Scheduled] --> B[Interview Conducted]\n    B --> C[Record Score & Notes]\n    C --> D{Result?}\n    D -->|Pass| E[Candidate Promoted to Trainee]\n    D -->|Fail| F[Candidate Archived]"],

['CandidateRecordMedicalCheckUps','Rekam MCU Kandidat','Candidate Medical Checkups','候補者健康診断記録',
 'Mencatat hasil pemeriksaan kesehatan (Medical Check-Up) setiap kandidat, termasuk tanggal MCU, fasilitas kesehatan, hasil per parameter, dan keputusan layak/tidak layak.',
 'Records the medical check-up results for each candidate including MCU date, health facility, results per health parameter, and fit/unfit decision.',
 '各候補者の健康診断結果（診断日、医療機関、各項目の結果、適性判定）を記録します。',
 ['View all medical checkup records','Add MCU record with date, clinic, and test results','Record individual test results (blood, vision, hearing, etc.)','Set overall result: fit / unfit / conditional','Unfit candidates are put on hold; fit candidates proceed'],
 "A[MCU Scheduled] --> B[Candidate Goes to Clinic]\n    B --> C[Record Each Test Result]\n    C --> D{Overall Result}\n    D -->|Fit| E[Candidate Proceeds]\n    D -->|Unfit| F[On Hold or Rejected]"],

['CandidateSubmissionDocuments','Dokumen Pengajuan Kandidat','Candidate Submission Documents','候補者提出書類',
 'Mencatat dokumen-dokumen yang diajukan kandidat untuk pengiriman ke Jepang, termasuk terjemahan, legalisasi, dan dokumen yang diperlukan pihak penerima.',
 'Records documents submitted by candidates for Japan placement, including translations, legalizations, and documents required by the acceptance organization.',
 '日本派遣に向けて候補者が提出した書類（翻訳文書、認証書類、受け入れ機関が必要とする書類など）を記録します。',
 ['View all submission documents per candidate','Track translation and legalization status','Upload final document versions','Monitor readiness before Japan departure','Coordinate with acceptance organization requirements'],
 "A[Documents Identified] --> B[Candidate Prepares Docs]\n    B --> C[Translate & Legalize]\n    C --> D[Submit to TMM]\n    D --> E[Forward to Japan]"],

// --- TRAINEE ---
['Trainees','Manajemen Peserta Pelatihan','Trainee Management','研修生管理',
 'Halaman utama menampilkan daftar peserta pelatihan yang telah lolos seleksi kandidat dan sedang menjalani program pelatihan pra-keberangkatan (bahasa Jepang, budaya, keterampilan kerja). Data mencakup batch pelatihan, LPK, institusi penerima, dan status kelulusan.',
 'The main page lists all trainees who passed candidate selection and are undergoing pre-departure training (Japanese language, culture, job skills). Records include training batch, LPK, acceptance organization assignment, and training pass/fail status.',
 'メインページは候補者選考を通過し、出発前研修（日本語、文化、職業スキル）を受けている全研修生の一覧を表示します。研修バッチ、LPK、受け入れ機関の配属、研修合否状況を管理します。',
 ['View list of all active trainees by batch','Filter by LPK, acceptance org, or training status','Click trainee to view full profile with tabs (education, scores, documents, family)','Add trainee when a candidate is promoted after selection','Update status after training completion or test results'],
 "A[Candidate Passes Selection] --> B[Assigned to Training Batch]\n    B --> C[Pre-Departure Training at LPK]\n    C --> D[Language & Skill Tests]\n    D --> E{Pass?}\n    E -->|Yes| F[Promoted to Apprentice]\n    E -->|No| G[Extended Training or Return]"],

['TraineeCertifications','Sertifikasi Peserta Pelatihan','Trainee Certifications','研修生資格',
 'Mencatat sertifikat yang diperoleh peserta pelatihan selama program, seperti sertifikat bahasa Jepang (JLPT N5/N4), sertifikat keahlian teknis, atau sertifikat keselamatan kerja.',
 'Records certifications obtained by trainees during the training program, such as Japanese language certificates (JLPT N5/N4), technical skill certificates, or workplace safety certificates.',
 '研修期間中に研修生が取得した資格（JLPT N5/N4等の日本語検定、技術証明書、安全衛生資格など）を記録します。',
 ['View certifications per trainee','Add certificate with name, issuing body, and date','Upload certificate scan','Filter by certificate type or trainee batch','Used to verify readiness for Japan departure'],
 "A[Training Completed] --> B[Trainee Takes Exam]\n    B --> C{Pass?}\n    C -->|Yes| D[Certificate Issued]\n    C -->|No| E[Retake or Continue]\n    D --> F[Uploaded to Profile]"],

['TraineeCourses','Kursus Peserta Pelatihan','Trainee Courses','研修生コース',
 'Mencatat kursus yang diikuti peserta pelatihan dalam program pra-keberangkatan: kursus bahasa Jepang, budaya Jepang, teknis pekerjaan, dan keselamatan kerja.',
 'Records courses attended by trainees during the pre-departure program: Japanese language, Japanese culture, technical job training, and workplace safety.',
 '出発前プログラムで研修生が受講したコース（日本語、日本文化、職業訓練、安全衛生）を記録します。',
 ['View all courses per trainee','Add course entry with name, instructor, hours, and score','Track attendance and completion','Filter by course type or training batch','Completion required before trainee can be promoted'],
 "A[Training Schedule Set] --> B[Trainee Attends Course]\n    B --> C[Record Attendance & Score]\n    C --> D{All Required Courses?}\n    D -->|Yes| E[Trainee Eligible for Promotion]\n    D -->|No| F[Continue Training]"],

['TraineeEducations','Pendidikan Peserta Pelatihan','Trainee Educations','研修生学歴',
 'Riwayat pendidikan formal peserta pelatihan. Data ini melengkapi profil peserta untuk keperluan pengajuan visa dan dokumen ke Jepang.',
 'Formal educational history of each trainee. This data completes the trainee profile for visa applications and Japan-bound documents.',
 '各研修生の学歴。ビザ申請や日本向け書類のために研修生プロフィールを補完するデータです。',
 ['View education records per trainee','Verify data matches candidate record','Update if education was completed during training period','Used for visa application form filling','Cross-reference with diploma documents submitted'],
 "A[Trainee Profile Created] --> B[Education Data Carried Over from Candidate]\n    B --> C[Staff Verifies with Documents]\n    C --> D[Correct if Needed]\n    D --> E[Used in Visa Application]"],

['TraineeExperiences','Pengalaman Peserta Pelatihan','Trainee Work Experiences','研修生職歴',
 'Riwayat pekerjaan peserta pelatihan sebelum dan selama program pelatihan. Diperlukan untuk profil lengkap pengajuan ke institusi penerima di Jepang.',
 'Work history of trainees before and during the training program. Required for a complete profile submission to the acceptance organization in Japan.',
 '研修生の研修前および研修中の職歴。日本の受け入れ機関への完全なプロフィール提出に必要です。',
 ['View work experience records per trainee','Verify data carried over from candidate stage','Add new experience if gained during training','Used when matching trainees with job positions in Japan','Cross-check with acceptance organization requirements'],
 "A[Candidate Promoted to Trainee] --> B[Work History Carried Over]\n    B --> C[Staff Reviews for Japan Submission]\n    C --> D[Update if New Experience Added]\n    D --> E[Submitted with Trainee Profile to Japan]"],

['TraineeFamilies','Data Keluarga Peserta Pelatihan','Trainee Families','研修生家族情報',
 'Data anggota keluarga peserta pelatihan untuk keperluan administrasi, dokumen visa, dan kontak darurat selama masa pelatihan.',
 'Family member data for each trainee for administration, visa documentation, and emergency contact purposes during the training period.',
 '研修期間中の管理業務、ビザ書類、緊急連絡のために各研修生の家族情報を管理します。',
 ['View family records per trainee','Verify and update family data from candidate stage','Add family members if status changed (marriage, new child)','Emergency contact used if issues arise during training','Family data required for visa application forms'],
 "A[Candidate Promoted to Trainee] --> B[Family Data Carried Over]\n    B --> C[Verify Current Status]\n    C --> D[Update if Changed]\n    D --> E[Used for Visa & Emergency Contact]"],

['TraineeFamilyStories','Cerita Keluarga Peserta Pelatihan','Trainee Family Stories','研修生家族ストーリー',
 'Catatan perkembangan atau cerita keluarga peserta pelatihan selama masa pelatihan. Digunakan untuk monitoring kesejahteraan peserta dan komunikasi dengan keluarga.',
 'Updates and stories about trainees\' families during the training period. Used for welfare monitoring and communication between TMM and families.',
 '研修期間中の研修生家族に関する更新情報やストーリー。TMMと家族との福祉モニタリングおよびコミュニケーションに使用します。',
 ['View family story entries per trainee','Add story or update about family situation','Record welfare visits or family communication','Flag if family issues might affect trainee performance','Archived for reference after trainee departs to Japan'],
 "A[Regular Monitoring] --> B[Staff Contacts Family]\n    B --> C[Record Family Update/Story]\n    C --> D[Flag Issues if Any]\n    D --> E[Follow Up Actions if Needed]"],

['TraineeInstallments','Cicilan Biaya Peserta Pelatihan','Trainee Payment Installments','研修生分割払い',
 'Mencatat rencana dan realisasi pembayaran biaya program pelatihan oleh peserta atau keluarga mereka. Termasuk jadwal cicilan, jumlah yang sudah dibayar, dan sisa tagihan.',
 'Records the payment plan and actual payments for training program fees by trainees or their families, including installment schedule, amount paid, and remaining balance.',
 '研修プログラム費用の支払い計画と実際の支払い（分割払いスケジュール、支払済み金額、残高）を記録します。',
 ['View all payment records per trainee','Add installment payment when received','Track payment due dates and amounts','Generate outstanding balance report','Follow up on overdue payments'],
 "A[Training Fee Agreed] --> B[Payment Schedule Created]\n    B --> C[Trainee/Family Pays Installment]\n    C --> D[Staff Records Payment]\n    D --> E{Fully Paid?}\n    E -->|Yes| F[Account Cleared]\n    E -->|No| G[Follow Up Next Installment]"],

['TraineeScoreAverages','Rata-Rata Nilai Peserta Pelatihan','Trainee Score Averages','研修生平均スコア',
 'Menampilkan rata-rata nilai ujian dan tes pelatihan setiap peserta, dihitung dari seluruh tes yang telah diikuti. Digunakan untuk mengevaluasi kemajuan keseluruhan peserta pelatihan.',
 'Shows the average test and training scores per trainee, calculated from all tests taken. Used to evaluate the overall progress of each trainee in the program.',
 '受講した全テストから算出した各研修生の平均テストスコアを表示します。研修プログラムにおける各研修生の総合的な進捗状況を評価するために使用します。',
 ['View average score summary per trainee','Sort by score to identify top and low performers','Filter by batch or training period','Identify trainees needing additional support','Score averages used in final promotion decision'],
 "A[Multiple Tests Completed] --> B[Calculate Average Score]\n    B --> C[Display in Score Average Page]\n    C --> D{Above Threshold?}\n    D -->|Yes| E[Eligible for Promotion]\n    D -->|No| F[Additional Training Required]"],

['TraineeTrainingBatches','Batch Pelatihan','Training Batches','研修バッチ',
 'Mengelola pengelompokan peserta pelatihan berdasarkan batch/angkatan. Setiap batch memiliki tanggal mulai dan berakhir, LPK tempat pelatihan, dan target keberangkatan ke Jepang.',
 'Manages grouping of trainees by training batch/cohort. Each batch has a start date, end date, assigned LPK, and target Japan departure date.',
 '研修生を研修バッチ/コホートでグループ管理します。各バッチには開始日、終了日、担当LPK、日本出発目標日があります。',
 ['View all training batches with dates and LPK assignment','Add new batch with name, dates, and capacity','Assign trainees to a batch','Track batch progress (training, ready for departure, departed)','View all trainees in a specific batch'],
 "A[New Batch Created] --> B[Trainees Assigned to Batch]\n    B --> C[Training Period Begins]\n    C --> D[Batch Completes Training]\n    D --> E[Batch Members Depart to Japan]"],

['TraineeTrainingTestScores','Nilai Ujian Pelatihan','Training Test Scores','研修テストスコア',
 'Mencatat nilai setiap ujian atau tes yang diikuti peserta pelatihan. Termasuk jenis tes (bahasa Jepang, teknis, fisik), tanggal, nilai, dan kelulusan.',
 'Records scores for each test or exam taken by trainees. Includes test type (Japanese language, technical, physical), date, score, and pass/fail result.',
 '研修生が受けた各テスト・試験のスコアを記録します。テスト種別（日本語、技術、体力）、日付、スコア、合否結果を含みます。',
 ['View all test scores per trainee','Add test record with type, date, and score','Filter by test type or batch','Identify low-scoring trainees for remedial support','Scores feed into score averages calculation'],
 "A[Test Scheduled] --> B[Trainee Takes Test]\n    B --> C[Record Score]\n    C --> D[Update Score Average]\n    D --> E{Pass Threshold?}\n    E -->|Yes| F[Proceed to Next Module]\n    E -->|No| G[Remedial / Re-test]"],

// --- APPRENTICE ---
['Apprentices','Manajemen Peserta Magang','Apprentice Management','研修生（日本）管理',
 'Halaman utama menampilkan daftar semua peserta magang yang sedang atau telah bekerja di Jepang. Data mencakup nama, kode peserta, nama katakana, perusahaan penerima, prefektur Jepang, periode kerja, dan status magang.',
 'The main page lists all apprentices currently or previously working in Japan. Records include name, apprentice code, katakana name, acceptance organization, Japan prefecture, work period, and apprenticeship status.',
 'メインページは現在または過去に日本で就労している全研修生（アプレンティス）の一覧を表示します。氏名、研修生コード、カタカナ氏名、受け入れ機関、都道府県、就労期間、研修状況を管理します。',
 ['View all apprentices with status and organization','Filter by acceptance org, prefecture, or status','Click apprentice to view full profile with tabs (family, documents, flights, certifications)','Add when trainee departs for Japan','Update status on return or completion'],
 "A[Trainee Passes Training] --> B[Assigned to Acceptance Org]\n    B --> C[COE & Visa Processed]\n    C --> D[Departs to Japan]\n    D --> E[Apprenticeship Period Active]\n    E --> F{Complete?}\n    F -->|Yes| G[Returns to Indonesia]\n    F -->|Extended| E"],

['ApprenticeCertifications','Sertifikasi Peserta Magang','Apprentice Certifications','研修生資格（日本）',
 'Mencatat sertifikat yang diperoleh peserta magang selama bekerja di Jepang, seperti sertifikat keterampilan teknis, JLPT, sertifikat keselamatan, atau kualifikasi dari perusahaan penerima.',
 'Records certifications obtained by apprentices while working in Japan, such as technical skill certificates, JLPT, safety qualifications, or company-issued certifications.',
 '日本での就労中に研修生が取得した資格（技能証明書、JLPT、安全資格、企業認定資格など）を記録します。',
 ['View certifications earned in Japan per apprentice','Add certificate with name, issuing body, and date','Upload certificate scan','Used for career profiling upon return to Indonesia','Supports post-apprenticeship employment matching'],
 "A[Apprentice Working in Japan] --> B[Takes Skill/Language Test]\n    B --> C{Pass?}\n    C -->|Yes| D[Certificate Issued in Japan]\n    C -->|No| E[Retake Later]\n    D --> F[Recorded in TMM System]"],

['ApprenticeCourses','Kursus Peserta Magang','Apprentice Courses','研修生コース（日本）',
 'Mencatat kursus atau pelatihan yang diikuti peserta magang selama di Jepang, baik yang diselenggarakan oleh perusahaan penerima maupun lembaga independen.',
 'Records courses or training programs attended by apprentices while in Japan, whether organized by the acceptance company or independent institutions.',
 '日本滞在中に研修生が受講したコース・研修（受け入れ企業主催または独立機関）を記録します。',
 ['View courses per apprentice','Add course with name, provider, hours, and result','Track skill development during Japan stay','Cross-reference with acceptance org requirements','Used for post-return career matching'],
 "A[Apprentice in Japan] --> B[Enrolls in Course]\n    B --> C[Attends Training]\n    C --> D[Receives Certificate or Score]\n    D --> E[Recorded in TMM System]"],

['ApprenticeDocumentManagementDashboards','Dashboard Manajemen Dokumen Magang','Apprentice Document Dashboards','研修生書類管理ダッシュボード',
 'Dashboard ringkasan status kelengkapan dokumen untuk semua peserta magang. Memperlihatkan persentase kelengkapan, dokumen yang belum lengkap, dan dokumen yang sudah kadaluarsa.',
 'Summary dashboard showing document completeness status for all apprentices. Shows completion percentage, missing documents, and expired documents.',
 '全研修生の書類完備状況を示すサマリーダッシュボード。完備率、不足書類、期限切れ書類を表示します。',
 ['View document status overview for all apprentices','Filter by org, batch, or document status','Identify apprentices with missing or expiring documents','Drill down to individual apprentice document list','Generate compliance report for management'],
 "A[All Apprentice Documents Loaded] --> B[Calculate Completeness]\n    B --> C[Flag Missing / Expired]\n    C --> D[Display Dashboard]\n    D --> E[Staff Takes Action on Flagged Items]"],

['ApprenticeEducations','Pendidikan Peserta Magang','Apprentice Educations','研修生学歴（日本）',
 'Riwayat pendidikan peserta magang yang digunakan dalam profil pengajuan ke Jepang dan dokumen resmi selama masa magang.',
 'Educational history of apprentices used in the Japan submission profile and official documents during the apprenticeship period.',
 '研修生の学歴（日本派遣申請プロフィールおよびアプレンティシップ期間中の公式書類に使用）を管理します。',
 ['View education records per apprentice','Verify data matches trainee and candidate records','Update if additional education completed','Used in official Japanese organization documentation','Required for visa renewal processes'],
 "A[Candidate to Trainee to Apprentice] --> B[Education Data Carried Forward]\n    B --> C[Verified by Staff]\n    C --> D[Used in Japan Documents]\n    D --> E[Updated if New Education Completed]"],

['ApprenticeExperiences','Pengalaman Kerja Peserta Magang','Apprentice Work Experiences','研修生職歴（日本）',
 'Rekaman pengalaman kerja peserta magang termasuk pekerjaan di Indonesia sebelum berangkat dan pengalaman kerja di Jepang yang sedang atau sudah selesai.',
 'Work experience records for apprentices including pre-departure Indonesia jobs and current/completed Japan work experience.',
 '研修生の職歴（出発前のインドネシアでの仕事および日本での現在・過去の就労経験）を記録します。',
 ['View all work experience per apprentice','Add Japan work experience as it occurs','Record company, position, and duration','Used for comprehensive career profile','Important for post-return employment support'],
 "A[Apprentice Begins Work in Japan] --> B[Record Job Position & Company]\n    B --> C[Update Duration Periodically]\n    C --> D[Record Completion on Return]\n    D --> E[Full Career Profile Built]"],

['ApprenticeFamilies','Data Keluarga Peserta Magang','Apprentice Families','研修生家族情報（日本）',
 'Data anggota keluarga peserta magang yang aktif di Jepang. Digunakan untuk komunikasi darurat dan keperluan dokumen remittance atau asuransi.',
 'Family data for apprentices currently active in Japan. Used for emergency communication and documentation needs like remittances or insurance.',
 '日本でアクティブな研修生の家族情報。緊急連絡や送金・保険の書類手続きに使用します。',
 ['View family records per apprentice','Verify current contact information is up to date','Update if family situation changes while in Japan','Emergency contact used in case of accident or illness','Required for remittance and insurance documentation'],
 "A[Apprentice in Japan] --> B[Family Contact Maintained]\n    B --> C[Staff Can Reach Family if Emergency]\n    C --> D[Updated if Situation Changes]\n    D --> E[Used for Insurance & Remittance Forms]"],

['ApprenticeFamilyStories','Cerita Keluarga Peserta Magang','Apprentice Family Stories','研修生家族ストーリー（日本）',
 'Catatan cerita, berita, atau perkembangan dari keluarga peserta magang yang sedang di Jepang. Membantu pemantauan kesejahteraan dan komunikasi antara TMM, keluarga, dan peserta.',
 'Stories, updates, and news from families of apprentices in Japan. Supports welfare monitoring and communication between TMM, families, and the apprentices themselves.',
 '日本にいる研修生の家族からのストーリー・近況・ニュースを記録します。TMM、家族、研修生間の福祉モニタリングとコミュニケーションを支援します。',
 ['View family stories per apprentice','Add story when family contacts TMM or during welfare visits','Record welfare issues that need follow-up','Share positive updates with apprentice as encouragement','Archive for post-return counseling reference'],
 "A[Family Contacts TMM] --> B[Staff Records Story/Update]\n    B --> C{Issue Detected?}\n    C -->|Yes| D[Escalate & Follow Up]\n    C -->|No| E[Archive as Welfare Record]"],

['ApprenticeFlights','Data Penerbangan Peserta Magang','Apprentice Flights','研修生フライト記録',
 'Mencatat data penerbangan peserta magang: keberangkatan dari Indonesia ke Jepang dan kepulangan ke Indonesia. Termasuk maskapai, nomor penerbangan, tanggal, dan bandara.',
 'Records flight data for apprentices: departure from Indonesia to Japan and return flights. Includes airline, flight number, date, and airport.',
 '研修生のフライト情報（インドネシアから日本への出発便および帰国便）を記録します。航空会社、便名、日付、空港を含みます。',
 ['View all flight records per apprentice','Add departure flight details before Japan departure','Add return flight when apprentice is coming back','Filter by departure date or airline','Used for airport pickup coordination and records'],
 "A[Japan Departure Date Confirmed] --> B[Book Flight Ticket]\n    B --> C[Record Flight Details in TMM]\n    C --> D[Apprentice Departs]\n    D --> E[Record Return Flight Details]\n    E --> F[Apprentice Arrives Home]"],

['ApprenticeOrders','Pesanan Peserta Magang','Apprentice Orders','研修生オーダー',
 'Mencatat permintaan atau pesanan dari peserta magang yang sedang di Jepang, seperti permintaan dokumen, pengiriman barang, atau layanan lainnya dari TMM.',
 'Records requests or orders from apprentices currently in Japan, such as document requests, item shipments, or other services from TMM.',
 '日本にいる研修生からのリクエスト・オーダー（書類依頼、荷物発送、その他TMMサービス）を記録します。',
 ['View all orders per apprentice','Add order with type, description, and priority','Update order status as it is processed','Track shipping or processing timeline','Notify apprentice when order is fulfilled'],
 "A[Apprentice Sends Request] --> B[Staff Records Order]\n    B --> C[Order Processed]\n    C --> D{Fulfilled?}\n    D -->|Yes| E[Mark Complete & Notify]\n    D -->|No| F[Follow Up / Escalate]"],

['ApprenticeRecordCoeVisas','Rekam COE & Visa Peserta Magang','Apprentice COE & Visa Records','研修生COE・ビザ記録',
 'Mencatat proses Certificate of Eligibility (COE) dan visa peserta magang: tanggal pengajuan, tanggal terbit, masa berlaku, dan perpanjangan. COE adalah dokumen wajib sebelum visa magang Jepang diterbitkan.',
 'Records the Certificate of Eligibility (COE) and visa process for each apprentice: application date, issue date, validity period, and renewals. COE is mandatory before a Japan trainee visa is issued.',
 '各研修生のCOE（在留資格認定証明書）とビザのプロセス（申請日、発行日、有効期間、更新）を記録します。COEは日本の研修ビザ発行前に必須の書類です。',
 ['View COE and visa records per apprentice','Add COE application record with submission date','Update with COE approval and issue date','Record visa application and approval dates','Track expiry dates for renewal planning'],
 "A[Acceptance Org Applies for COE in Japan] --> B[COE Approved]\n    B --> C[COE Sent to Indonesia]\n    C --> D[Apprentice Applies for Visa]\n    D --> E[Visa Issued]\n    E --> F[Apprentice Can Depart]"],

['ApprenticeRecordMedicalCheckUps','Rekam MCU Peserta Magang','Apprentice Medical Checkups','研修生健康診断記録（日本）',
 'Mencatat pemeriksaan kesehatan peserta magang sebelum keberangkatan ke Jepang dan selama masa magang, termasuk MCU pre-departure dan periodic checkup di Jepang.',
 'Records medical checkups for apprentices before Japan departure and during the apprenticeship, including pre-departure MCU and periodic checkups in Japan.',
 '出発前および研修期間中の研修生の健康診断（出発前MCUと日本での定期健診）を記録します。',
 ['View MCU records per apprentice','Add pre-departure MCU record with all test results','Record Japan-based periodic health checkup results','Flag health issues that need follow-up','Required documentation for Japan employer and immigration'],
 "A[Pre-Departure MCU Scheduled] --> B[Apprentice Attends Clinic]\n    B --> C[Record All Test Results]\n    C --> D{Fit to Travel?}\n    D -->|Yes| E[Cleared for Departure]\n    D -->|No| F[Medical Follow Up Before Departure]"],

['ApprenticeRecordPasports','Rekam Paspor Peserta Magang','Apprentice Passport Records','研修生パスポート記録',
 'Mencatat data paspor peserta magang: nomor paspor, tanggal terbit, tanggal kadaluarsa, dan perpanjangan. Paspor harus berlaku minimal 1 tahun sebelum keberangkatan ke Jepang.',
 'Records passport data for apprentices: passport number, issue date, expiry date, and renewals. Passports must be valid for at least 1 year before Japan departure.',
 '研修生のパスポート情報（番号、発行日、有効期限、更新）を記録します。日本出発前に少なくとも1年以上の有効期限が必要です。',
 ['View passport records per apprentice','Add passport number, issue, and expiry dates','Set alert for expiring passports','Track renewal process when passport is near expiry','Required document for COE and visa processing'],
 "A[Candidate Has Passport] --> B[Record Passport Details]\n    B --> C[Track Expiry Date]\n    C --> D{Expiring Soon?}\n    D -->|Yes| E[Initiate Renewal]\n    D -->|No| F[Monitor Ongoing]\n    E --> G[New Passport Recorded]"],

['ApprenticeSubmissionDocuments','Dokumen Pengajuan Magang','Apprentice Submission Documents','研修生提出書類（日本）',
 'Mencatat dokumen resmi yang diajukan untuk penempatan peserta magang di Jepang: surat izin orang tua, perjanjian kontrak, formulir kerjasama institusi penerima, dll.',
 'Records official documents submitted for placing apprentices in Japan: parental consent letters, contract agreements, acceptance organization cooperation forms, etc.',
 '日本への研修生配置のために提出した公式書類（保護者同意書、契約書、受け入れ機関協力申請書など）を記録します。',
 ['View all submission documents per apprentice','Add document with type, date, and uploaded scan','Track submission and approval status','Coordinate with acceptance organization for their required forms','Archive for legal and compliance reference'],
 "A[Apprentice Selected for Japan] --> B[Gather Required Documents]\n    B --> C[Submit to TMM]\n    C --> D[Forward to Japan Partner]\n    D --> E[Approval Received]\n    E --> F[Departure Authorized]"],

// --- STAKEHOLDERS ---
['AcceptanceOrganizations','Organisasi Penerima','Acceptance Organizations','受け入れ機関',
 'Halaman utama menampilkan daftar perusahaan dan organisasi di Jepang yang menerima peserta magang TMM. Data mencakup nama perusahaan, alamat, prefektur, industri, kapasitas, dan kontak person di Jepang.',
 'The main page lists Japanese companies and organizations that accept TMM apprentices. Records include company name, address, Japan prefecture, industry type, capacity, and Japan contact person.',
 'メインページはTMM研修生を受け入れる日本の企業・機関の一覧を表示します。会社名、住所、都道府県、業種、受け入れ定員、日本の担当者連絡先を管理します。',
 ['View all acceptance organizations list','Filter by prefecture, industry, or capacity','Click an org to view its profile and placed apprentices','Add new partner company after cooperation agreement','Update contact info or capacity as needed'],
 "A[Partnership Agreement Signed] --> B[Org Added to TMM]\n    B --> C[Requirements & Capacity Defined]\n    C --> D[Trainees Matched & Placed]\n    D --> E[Apprentices Working at Org]\n    E --> F[Annual Review & Renewal]"],

['AcceptanceOrganizationStories','Cerita Organisasi Penerima','Acceptance Organization Stories','受け入れ機関ストーリー',
 'Cerita sukses dan testimoni dari organisasi penerima di Jepang mengenai pengalaman mereka menerima peserta magang TMM. Digunakan sebagai promosi dan referensi kemitraan.',
 'Success stories and testimonials from Japanese acceptance organizations about their experience with TMM apprentices. Used for promotion and partnership reference.',
 '日本の受け入れ機関からのTMM研修生受け入れ体験に関する成功事例・証言。プロモーションとパートナーシップ参照のために使用します。',
 ['View stories from acceptance organizations','Add new story with company name, content, and date','Include photos if available','Feature stories on TMM promotional materials','Helps attract new candidates and LPK partners'],
 "A[Positive Apprentice Experience] --> B[Acceptance Org Shares Story]\n    B --> C[Staff Records in TMM]\n    C --> D[Story Published/Shared]\n    D --> E[Used in Promotion & Outreach]"],

['CooperativeAssociations','Asosiasi Koperasi','Cooperative Associations','協同組合',
 'Mengelola data asosiasi koperasi (Kumiai) Jepang yang bermitra dengan TMM sebagai perantara penempatan magang. Setiap kumiai memiliki jaringan perusahaan anggota yang dapat menerima peserta magang.',
 'Manages data of Japanese cooperative associations (Kumiai) partnering with TMM as intermediaries for apprentice placement. Each kumiai has a network of member companies that can accept apprentices.',
 'TMM と研修生配置の仲介者としてパートナーシップを結んでいる日本の協同組合（組合）のデータを管理します。各組合は研修生を受け入れ可能な加盟企業ネットワークを持っています。',
 ['View all cooperative associations','Filter by region or industry type','Add new kumiai with contact and capacity info','Link kumiai to their acceptance organizations','Track placements made through each association'],
 "A[Kumiai Partnership Established] --> B[Kumiai Added to TMM]\n    B --> C[Member Companies Listed]\n    C --> D[Trainees Matched via Kumiai]\n    D --> E[Apprentices Placed at Member Companies]"],

['CooperativeAssociationStories','Cerita Asosiasi Koperasi','Cooperative Association Stories','協同組合ストーリー',
 'Cerita dan testimoni dari asosiasi koperasi Jepang tentang program magang TMM. Digunakan untuk memperkuat hubungan kemitraan dan promosi program.',
 'Stories and testimonials from Japanese cooperative associations about the TMM apprenticeship program. Used to strengthen partnership relationships and program promotion.',
 '日本の協同組合からのTMM研修プログラムに関するストーリー・証言。パートナーシップ強化とプログラムプロモーションに使用します。',
 ['View stories from cooperative associations','Add story with association name and content','Include photos or achievement data','Share stories in recruitment and marketing','Archive for historical reference'],
 "A[Association Shares Feedback] --> B[Staff Records Story]\n    B --> C[Reviewed & Approved]\n    C --> D[Published in TMM]\n    D --> E[Used for Promotion & Partner Relations]"],

['SpecialSkillSupportInstitutions','Lembaga Dukungan Keahlian Khusus','Special Skill Support Institutions','特定技能支援機関',
 'Mengelola data lembaga dukungan keahlian khusus (Tokutei Gino) yang mendukung peserta dengan visa keahlian khusus di Jepang. Lembaga ini memberikan dukungan kehidupan, bahasa, dan adaptasi bagi peserta.',
 'Manages data of special skill support institutions (Tokutei Gino support orgs) that support participants with special skill visas in Japan. These institutions provide life support, language assistance, and adaptation support.',
 '日本での特定技能ビザ保持者をサポートする特定技能登録支援機関のデータを管理します。これらの機関は生活支援、語学支援、適応支援を提供します。',
 ['View all registered special skill support institutions','Add new institution with registration number and services','Link to apprentices using special skill visa type','Track support services provided','Verify institution registration status with Japanese authority'],
 "A[Institution Registered with TMM] --> B[Linked to Special Skill Apprentices]\n    B --> C[Provides Required Support Services]\n    C --> D[Reports Status to TMM]\n    D --> E[Compliance Verified]"],

['VocationalTrainingInstitutions','Lembaga Pelatihan Vokasi (LPK)','Vocational Training Institutions','職業訓練機関（LPK）',
 'Mengelola data Lembaga Pelatihan Kerja (LPK) di Indonesia yang bermitra dengan TMM untuk melatih kandidat dan peserta magang sebelum keberangkatan ke Jepang. LPK menyelenggarakan pelatihan bahasa Jepang dan keterampilan kerja.',
 'Manages Indonesian Vocational Training Institute (LPK) data that partner with TMM to train candidates and trainees before Japan departure. LPKs conduct Japanese language and job skills training.',
 'TMM と連携して候補者・研修生に日本出発前の職業訓練（日本語・職業スキル）を提供するインドネシアの職業訓練機関（LPK）のデータを管理します。',
 ['View all partner LPK institutions','Filter by region or accreditation status','Click LPK to view its trainees and training history','Add new LPK with location, contact, and accreditation info','Track training outcomes per LPK'],
 "A[LPK Partnership Signed] --> B[LPK Registered in TMM]\n    B --> C[Candidates Assigned to LPK]\n    C --> D[Training Conducted]\n    D --> E[Trainees Evaluated]\n    E --> F[Results Reported to TMM]"],

['VocationalTrainingInstitutionStories','Cerita LPK','LPK Stories','LPKストーリー',
 'Cerita sukses dan testimoni dari Lembaga Pelatihan Kerja (LPK) mitra tentang hasil pelatihan dan peserta yang berhasil dikirim ke Jepang.',
 'Success stories and testimonials from partner LPK institutions about training outcomes and successfully placed apprentices sent to Japan.',
 'パートナーLPKからの研修成果と日本に派遣された研修生の成功事例・証言。',
 ['View stories from LPK institutions','Add story with LPK name, outcome, and date','Include photos of training activities','Use in candidate recruitment and LPK outreach','Archive for partnership review meetings'],
 "A[LPK Achieves Good Results] --> B[Story Submitted to TMM]\n    B --> C[Staff Records & Reviews]\n    C --> D[Published in TMM]\n    D --> E[Used in Outreach & Promotion]"],

['Stakeholders','Pemangku Kepentingan','Stakeholders','ステークホルダー',
 'Tampilan ringkasan semua pemangku kepentingan dalam ekosistem TMM: LPK, organisasi penerima, asosiasi koperasi, dan lembaga dukungan keahlian khusus.',
 'Summary overview of all stakeholders in the TMM ecosystem: LPKs, acceptance organizations, cooperative associations, and special skill support institutions.',
 'TMMエコシステムの全ステークホルダー（LPK、受け入れ機関、協同組合、特定技能支援機関）のサマリー概要。',
 ['View overview of all stakeholder types','Navigate to specific stakeholder category','See count of active partners per type','Access details for each partner organization','Use for partnership status review'],
 "A[LPK] --> C[TMM System]\n    B[Acceptance Org] --> C\n    D[Kumiai] --> C\n    E[Special Skill Inst.] --> C\n    C --> F[Candidate/Trainee/Apprentice Placed]"],

// --- MASTER DATA ---
['MasterBloodTypes','Master Golongan Darah','Blood Type Master','血液型マスター',
 'Data referensi jenis golongan darah (A, B, AB, O) yang digunakan di seluruh sistem TMM dalam formulir kandidat, peserta pelatihan, dan peserta magang.',
 'Reference data for blood types (A, B, AB, O) used throughout the TMM system in candidate, trainee, and apprentice forms.',
 '候補者・研修生・アプレンティスのフォームでTMMシステム全体で使用される血液型（A、B、AB、O）の参照データ。',
 ['View all blood type options','Add new blood type if needed','Edit display name','Used as dropdown option in profile forms','Rarely changed — verify before modifying'],
 "A[Profile Form Loaded] --> B[Blood Type Dropdown]\n    B --> C[Uses Blood Type Master]\n    C --> D[User Selects Type]\n    D --> E[Saved to Profile]"],

['MasterGenders','Master Jenis Kelamin','Gender Master','性別マスター',
 'Data referensi jenis kelamin yang digunakan dalam profil kandidat, peserta pelatihan, dan peserta magang di seluruh sistem.',
 'Reference data for gender options used in candidate, trainee, and apprentice profiles throughout the system.',
 'システム全体の候補者・研修生・アプレンティスのプロフィールで使用される性別の参照データ。',
 ['View gender options','Add or edit gender values','Used in all profile forms as dropdown','Keep consistent across all modules','Changes affect all existing records using this master'],
 "A[Profile Form Loaded] --> B[Gender Dropdown]\n    B --> C[Uses Gender Master]\n    C --> D[User Selects Value]\n    D --> E[Saved to Profile]"],

['MasterReligions','Master Agama','Religion Master','宗教マスター',
 'Data referensi agama yang digunakan dalam profil kandidat, peserta pelatihan, dan peserta magang (Islam, Kristen, Katolik, Hindu, Buddha, Konghucu, dll).',
 'Reference data for religion options used in profiles (Islam, Christian, Catholic, Hindu, Buddhist, Confucian, etc.).',
 'プロフィールで使用される宗教の参照データ（イスラム、キリスト教、カトリック、ヒンドゥー、仏教、儒教など）。',
 ['View religion options','Add or update religion values','Used in profile forms and official documents','Required for Japan placement documentation','Keep full official names for document accuracy'],
 "A[Profile Form] --> B[Religion Field]\n    B --> C[Religion Master Data]\n    C --> D[Selected & Saved]"],

['MasterMarriageStatuses','Master Status Perkawinan','Marriage Status Master','婚姻状況マスター',
 'Data referensi status perkawinan (belum menikah, menikah, cerai, duda/janda) yang digunakan dalam profil dan dokumen resmi.',
 'Reference data for marital status options (single, married, divorced, widowed) used in profiles and official documents.',
 'プロフィールおよび公式書類で使用される婚姻状況（未婚、既婚、離婚、寡婦/寡夫）の参照データ。',
 ['View marital status options','Add or edit values','Used in candidate/trainee/apprentice forms','Required for official Japan documents','Status affects some visa requirements'],
 "A[Profile Form] --> B[Marriage Status Dropdown]\n    B --> C[Master Data Used]\n    C --> D[Saved to Profile & Documents]"],

['MasterStratas','Master Strata Pendidikan','Education Strata Master','学歴区分マスター',
 'Data referensi jenjang pendidikan (SD, SMP, SMA, D1, D2, D3, S1, S2, S3) yang digunakan dalam formulir pendidikan kandidat dan peserta.',
 'Reference data for education levels (elementary, middle, high school, diploma, bachelor, master, doctorate) used in candidate and trainee education forms.',
 '候補者・研修生の学歴フォームで使用される学歴区分（小学校、中学校、高校、専門学校、学士、修士、博士）の参照データ。',
 ['View education strata options','Add or edit strata values','Used in education history forms','Required for profile and visa documentation','Consistent naming ensures accurate reporting'],
 "A[Education Form] --> B[Strata/Level Dropdown]\n    B --> C[Strata Master Data]\n    C --> D[Saved to Education Record]"],

['MasterDepartments','Master Departemen','Department Master','部署マスター',
 'Data referensi departemen kerja yang digunakan dalam penempatan peserta magang di organisasi penerima Jepang (produksi, perakitan, pengemasan, finishing, dll).',
 'Reference data for work departments used in apprentice placement at Japanese acceptance organizations (production, assembly, packaging, finishing, etc.).',
 '日本の受け入れ機関での研修生配置に使用される部署の参照データ（製造、組立、包装、仕上げなど）。',
 ['View all department options','Add new department type','Edit existing department names','Used in apprentice work assignment','Filter reports by department to analyze performance'],
 "A[Apprentice Placement] --> B[Assign to Department]\n    B --> C[Department Master Used]\n    C --> D[Linked to Work Record]"],

['MasterJobCategories','Master Kategori Pekerjaan','Job Category Master','職種カテゴリーマスター',
 'Data referensi kategori pekerjaan tingkat atas yang mengelompokkan berbagai jenis pekerjaan: manufaktur, pertanian, jasa, konstruksi, dll.',
 'High-level job category reference data that groups different types of work: manufacturing, agriculture, services, construction, etc.',
 'さまざまな職種をグループ化する上位レベルの職種カテゴリ参照データ（製造業、農業、サービス業、建設業など）。',
 ['View all job categories','Add new category','Used to classify occupations and apprentice placements','Filter reporting by job category','Parent category for more specific occupations'],
 "A[Job Defined] --> B[Assigned to Job Category]\n    B --> C[Category Master Used]\n    C --> D[Used in Reporting & Matching]"],

['MasterOccupations','Master Pekerjaan','Occupation Master','職業マスター',
 'Data referensi daftar pekerjaan/jabatan spesifik yang digunakan dalam profil pengalaman kerja kandidat dan penempatan peserta magang di Jepang.',
 'Reference list of specific occupations/positions used in candidate work experience profiles and apprentice job placements in Japan.',
 '候補者の職歴プロフィールと日本での研修生就労配置に使用される具体的な職業・職位の参照リスト。',
 ['View all occupations','Add new occupation with name and category','Link occupation to job category','Used in work experience and placement forms','Supports better matching of skills to job requirements'],
 "A[Work Experience/Placement Form] --> B[Occupation Dropdown]\n    B --> C[Occupation Master]\n    C --> D[Selected & Saved]"],

['MasterOccupationCategories','Master Kategori Jabatan','Occupation Category Master','職業区分マスター',
 'Data referensi kategori jabatan yang mengelompokkan pekerjaan berdasarkan tingkatan atau jenis: teknisi, operator, pengawas, manajemen, dll.',
 'Reference data for occupation categories grouping jobs by level or type: technician, operator, supervisor, management, etc.',
 '技術者、オペレーター、監督者、管理職など、職種をレベルや種類でグループ化する職業区分の参照データ。',
 ['View occupation categories','Add or edit category values','Used to classify specific occupations','Helps in structured reporting and analysis','Parent structure for occupation master'],
 "A[Occupation Defined] --> B[Assigned to Category]\n    B --> C[Category Master Used]\n    C --> D[Used in Reports & Filtering]"],

['MasterJapanPrefectures','Master Prefektur Jepang','Japan Prefectures Master','日本都道府県マスター',
 'Daftar referensi 47 prefektur Jepang yang digunakan untuk menentukan lokasi organisasi penerima dan menandai lokasi kerja peserta magang di Jepang.',
 'Reference list of all 47 Japan prefectures used to specify the location of acceptance organizations and mark the work location of apprentices in Japan.',
 '受け入れ機関の所在地と日本での研修生就労場所を特定するために使用される日本47都道府県の参照リスト。',
 ['View all 47 Japan prefectures','Add or edit prefecture names (Japanese, Romaji)','Used in acceptance organization profiles','Used for filtering apprentices by location in Japan','Important for regional reporting and analysis'],
 "A[Acceptance Org Registered] --> B[Prefecture Selected]\n    B --> C[Prefecture Master Used]\n    C --> D[Org & Apprentices Linked to Prefecture]"],

['MasterPropinsis','Master Provinsi Indonesia','Indonesia Province Master','インドネシア州マスター',
 'Daftar 34 provinsi Indonesia sebagai referensi alamat kandidat, peserta pelatihan, dan peserta magang. Digunakan dalam form alamat dan analisis distribusi geografis.',
 'List of all 34 Indonesian provinces as address reference for candidates, trainees, and apprentices. Used in address forms and geographic distribution analysis.',
 '候補者・研修生・アプレンティスの住所参照として使用されるインドネシア34州のリスト。住所フォームと地理分布分析に使用します。',
 ['View all provinces','Edit province names if needed','Used as parent for Kabupaten selector','Geographic filter for reporting','Shows distribution of candidates/trainees by region'],
 "A[Address Form] --> B[Province Dropdown]\n    B --> C[Province Master]\n    C --> D[Kabupaten Loaded for Selected Province]"],

['MasterKabupatens','Master Kabupaten/Kota','District/City Master','県/市マスター（インドネシア）',
 'Daftar kabupaten dan kota di seluruh Indonesia, dikelompokkan per provinsi. Digunakan dalam formulir alamat kandidat, peserta pelatihan, dan peserta magang.',
 'List of all Indonesian districts and cities, grouped by province. Used in address forms for candidates, trainees, and apprentices.',
 '州ごとにグループ化されたインドネシアの全県・市のリスト。候補者・研修生・アプレンティスの住所フォームに使用します。',
 ['View districts/cities per province','Filter by province','Used as second-level in address hierarchy','Loaded dynamically based on province selection','Used in geographic reporting'],
 "A[Province Selected] --> B[Load Kabupaten List]\n    B --> C[User Selects Kabupaten]\n    C --> D[Kecamatan List Loaded]"],

['MasterKecamatans','Master Kecamatan','Sub-district Master','郡マスター（インドネシア）',
 'Daftar kecamatan (sub-distrik) Indonesia yang digunakan dalam formulir alamat detail kandidat, peserta pelatihan, dan peserta magang.',
 'List of Indonesian sub-districts used in detailed address forms for candidates, trainees, and apprentices.',
 'インドネシアの郡のリスト。候補者・研修生・アプレンティスの詳細住所フォームに使用します。',
 ['View sub-districts per district','Loaded dynamically from kabupaten selection','Used in address forms','Third level in Indonesian address hierarchy','Used for detailed geographic reporting'],
 "A[Kabupaten Selected] --> B[Load Kecamatan List]\n    B --> C[User Selects Kecamatan]\n    C --> D[Kelurahan List Loaded]"],

['MasterKelurahans','Master Kelurahan/Desa','Village Master','村/字マスター（インドネシア）',
 'Daftar kelurahan dan desa Indonesia yang digunakan dalam formulir alamat paling detail dalam profil kandidat, peserta pelatihan, dan peserta magang.',
 'List of Indonesian villages and urban wards used in the most detailed address fields in candidate, trainee, and apprentice profiles.',
 'インドネシアの村・字のリスト。候補者・研修生・アプレンティスのプロフィールの最詳細な住所フィールドに使用します。',
 ['View villages per sub-district','Loaded dynamically from kecamatan selection','Used as fourth level in Indonesian address forms','Enables precise location data for government documents','Complete address hierarchy: Propinsi → Kabupaten → Kecamatan → Kelurahan'],
 "A[Kecamatan Selected] --> B[Load Kelurahan List]\n    B --> C[User Selects Kelurahan]\n    C --> D[Complete Address Stored]"],

['MasterCurrencies','Master Mata Uang','Currency Master','通貨マスター',
 'Data referensi mata uang yang digunakan dalam transaksi keuangan sistem TMM: IDR (Rupiah), JPY (Yen), USD, dll.',
 'Reference data for currencies used in TMM financial transactions: IDR (Rupiah), JPY (Yen), USD, etc.',
 'TMMの財務取引で使用される通貨の参照データ（IDRルピア、JPY円、USDなど）。',
 ['View all currency options','Add new currency with code and name','Set exchange rate if used','Used in journal entries and financial forms','Ensure correct currency code for international transactions'],
 "A[Financial Transaction] --> B[Currency Selected]\n    B --> C[Currency Master]\n    C --> D[Amount Recorded in Selected Currency]"],

['MasterPaymentMethods','Master Metode Pembayaran','Payment Method Master','支払方法マスター',
 'Data referensi metode pembayaran yang digunakan dalam pencatatan transaksi keuangan: transfer bank, tunai, kartu kredit, dll.',
 'Reference data for payment methods used in financial transaction records: bank transfer, cash, credit card, etc.',
 '財務取引記録で使用される支払方法の参照データ（銀行振込、現金、クレジットカードなど）。',
 ['View payment method options','Add new payment method','Used in installment and journal forms','Helps categorize transactions for reporting','Keep current with accepted payment types'],
 "A[Payment Recorded] --> B[Payment Method Selected]\n    B --> C[Method Master Used]\n    C --> D[Transaction Categorized]"],

['MasterTransactionCategories','Master Kategori Transaksi','Transaction Category Master','取引カテゴリーマスター',
 'Data referensi kategori transaksi keuangan yang digunakan untuk mengklasifikasikan pendapatan dan pengeluaran dalam sistem akuntansi TMM.',
 'Reference data for financial transaction categories used to classify income and expenses in the TMM accounting system.',
 'TMM会計システムで収入・支出を分類するための財務取引カテゴリの参照データ。',
 ['View transaction categories','Add new category with type (income/expense)','Used in journal entries and financial reports','Enables structured financial reporting','Link to chart of accounts if needed'],
 "A[Journal Entry Created] --> B[Category Selected]\n    B --> C[Transaction Category Master]\n    C --> D[Entry Classified for Reporting]"],

['MasterFamilyConnections','Master Hubungan Keluarga','Family Connection Master','家族関係マスター',
 'Data referensi jenis hubungan keluarga yang digunakan dalam formulir data keluarga: ayah, ibu, kakak, adik, suami, istri, anak, dll.',
 'Reference data for family relationship types used in family data forms: father, mother, older sibling, younger sibling, husband, wife, child, etc.',
 '家族データフォームで使用される家族関係種別の参照データ（父、母、兄弟姉妹、夫、妻、子供など）。',
 ['View family relationship options','Add or edit relationship types','Used in candidate/trainee/apprentice family forms','Ensures consistent relationship terminology','Required for complete family data in official documents'],
 "A[Family Data Form] --> B[Relationship Dropdown]\n    B --> C[Family Connection Master]\n    C --> D[Saved to Family Record]"],

['MasterCandidateInterviewResults','Master Hasil Wawancara Kandidat','Candidate Interview Result Master','候補者面接結果マスター',
 'Data referensi hasil wawancara kandidat: Lulus, Tidak Lulus, Ditunda, Perlu Wawancara Ulang. Digunakan dalam formulir rekam wawancara.',
 'Reference data for candidate interview results: Passed, Failed, Deferred, Needs Re-interview. Used in interview record forms.',
 '候補者面接結果の参照データ（合格、不合格、保留、再面接必要）。面接記録フォームで使用します。',
 ['View interview result options','Add or edit result values','Used in candidate interview record forms','Determines next action for each candidate','Consistent values enable filtered reporting'],
 "A[Interview Conducted] --> B[Result Recorded]\n    B --> C[Interview Result Master]\n    C --> D[Candidate Status Updated]"],

['MasterCandidateInterviewTypes','Master Tipe Wawancara Kandidat','Candidate Interview Type Master','候補者面接種別マスター',
 'Data referensi jenis wawancara yang digunakan dalam proses seleksi kandidat: wawancara awal, wawancara teknis, wawancara dengan perusahaan Jepang, dll.',
 'Reference data for interview types in the candidate selection process: initial interview, technical interview, interview with Japanese company, etc.',
 '候補者選考プロセスで使用される面接種別の参照データ（初回面接、技術面接、日本企業との面接など）。',
 ['View interview type options','Add new interview type','Used to classify interview records','Enables filtering records by type','Different types may have different scoring criteria'],
 "A[Interview Scheduled] --> B[Interview Type Set]\n    B --> C[Interview Type Master]\n    C --> D[Interview Conducted & Recorded by Type]"],

['MasterInterviewResults','Master Hasil Wawancara','Interview Result Master','面接結果マスター',
 'Data referensi hasil umum untuk semua jenis wawancara dalam sistem TMM. Digunakan di berbagai modul wawancara.',
 'General reference data for interview results used across all interview types in the TMM system.',
 'TMMシステム全体の全面接種別で使用される面接結果の参照データ。',
 ['View all interview result options','Add new result type','Used across multiple interview modules','Consistent results across all stages','Basis for filtering and reporting candidates by result'],
 "A[Interview Record Created] --> B[Result Selected from Master]\n    B --> C[Candidate/Trainee Status Updated]\n    C --> D[Next Steps Determined]"],

['MasterMedicalCheckUpResults','Master Hasil MCU','Medical Checkup Result Master','健康診断結果マスター',
 'Data referensi hasil pemeriksaan kesehatan: Layak, Tidak Layak, Layak Bersyarat, Perlu Pemeriksaan Lanjutan. Digunakan di semua formulir rekam MCU.',
 'Reference data for medical checkup results: Fit, Unfit, Conditionally Fit, Further Examination Required. Used in all MCU record forms.',
 '健康診断結果の参照データ（適性あり、適性なし、条件付き適性、再検査要）。全MCU記録フォームで使用します。',
 ['View MCU result options','Add or update result values','Used in all medical checkup record forms','Determines if candidate/trainee can proceed','Conditionally fit cases need follow-up tracking'],
 "A[MCU Conducted] --> B[Results Recorded per Test]\n    B --> C[Overall Result Selected from Master]\n    C --> D[Candidate/Trainee Status Updated]"],

['MasterDocumentPreparednessStatuses','Master Status Kesiapan Dokumen','Document Preparedness Status Master','書類準備状況マスター',
 'Data referensi status kesiapan dokumen: Belum Ada, Sedang Diproses, Sudah Siap, Kadaluarsa. Digunakan dalam sistem manajemen dokumen.',
 'Reference data for document readiness status: Not Yet, In Process, Ready, Expired. Used in the document management system.',
 '書類準備状況の参照データ（未着手、処理中、準備完了、期限切れ）。書類管理システムで使用します。',
 ['View preparedness status options','Add or edit status values','Used in document tracking forms','Visual indicator of document completion','Drives dashboard completeness calculations'],
 "A[Document Required] --> B[Status Set: Not Yet]\n    B --> C[Processing: In Process]\n    C --> D[Completed: Ready]\n    D --> E[Monitor Expiry]"],

['MasterDocumentSubmissionStatuses','Master Status Pengiriman Dokumen','Document Submission Status Master','書類提出状況マスター',
 'Data referensi status pengiriman dokumen: Belum Dikumpulkan, Sudah Dikumpulkan, Diverifikasi, Ditolak. Digunakan dalam rekam pengumpulan dokumen.',
 'Reference data for document submission status: Not Submitted, Submitted, Verified, Rejected. Used in document collection records.',
 '書類提出状況の参照データ（未提出、提出済、確認済、却下）。書類収集記録で使用します。',
 ['View submission status options','Add or edit status values','Used in document submission tracking','Staff updates status when document is received and verified','Rejected documents trigger re-submission process'],
 "A[Document Required] --> B[Not Submitted]\n    B --> C[Candidate Submits]\n    C --> D[Staff Reviews]\n    D --> E{OK?}\n    E -->|Yes| F[Verified]\n    E -->|No| G[Rejected - Resubmit]"],

['MasterApprenticeCoeTypes','Master Tipe COE Magang','Apprentice COE Type Master','研修生COE種別マスター',
 'Data referensi jenis Certificate of Eligibility (COE) untuk peserta magang: Technical Intern Trainee (TITP), Specified Skilled Worker (SSW), dll. Menentukan jenis visa dan durasi magang.',
 'Reference data for Certificate of Eligibility types for apprentices: Technical Intern Trainee (TITP), Specified Skilled Worker (SSW), etc. Determines visa type and apprenticeship duration.',
 '研修生のCOE種別の参照データ（技能実習（TITP）、特定技能（SSW）など）。ビザ種別と研修期間を決定します。',
 ['View COE type options','Add or edit COE types','Used in COE and visa record forms','Determines maximum stay duration in Japan','Different types have different requirements and rights'],
 "A[Apprentice Placement Planned] --> B[COE Type Determined]\n    B --> C[COE Type Master Referenced]\n    C --> D[Application Filed with Correct Type]\n    D --> E[Visa Issued per Type Rules]"],

['MasterApprenticeDepartureDocuments','Master Dokumen Keberangkatan Magang','Apprentice Departure Document Master','研修生出発書類マスター',
 'Daftar master dokumen yang wajib disiapkan sebelum keberangkatan peserta magang ke Jepang: paspor, visa, COE, tiket pesawat, asuransi, surat perjanjian, dll.',
 'Master list of documents required before apprentice departure to Japan: passport, visa, COE, flight ticket, insurance, contract agreement, etc.',
 '日本出発前に必要な書類のマスターリスト（パスポート、ビザ、COE、航空券、保険、契約書など）。',
 ['View required departure documents','Add or edit document types in master list','Mark documents as mandatory or optional','Used as checklist template for departure clearance','Ensure no critical document is missed before departure'],
 "A[Apprentice Ready to Depart] --> B[Check Departure Document List]\n    B --> C[Verify Each Document]\n    C --> D{All Ready?}\n    D -->|Yes| E[Departure Cleared]\n    D -->|No| F[Obtain Missing Documents]"],

['MasterApprenticeSubmissionDocumentCategories','Master Kategori Dokumen Pengajuan Magang','Apprentice Submission Doc Category Master','研修生提出書類カテゴリーマスター',
 'Data referensi kategori dokumen pengajuan untuk penempatan magang di Jepang: dokumen pribadi, dokumen medis, dokumen hukum, dokumen pelatihan, dll.',
 'Reference data for document submission categories for Japan apprentice placement: personal documents, medical documents, legal documents, training documents, etc.',
 '日本研修生配置への提出書類カテゴリの参照データ（個人書類、医療書類、法的書類、研修書類など）。',
 ['View document category options','Add new category','Used to classify submission documents','Helps organize document requirements by type','Filter document status by category for review'],
 "A[Document Submission Required] --> B[Category Assigned]\n    B --> C[Category Master Referenced]\n    C --> D[Organized by Category for Review]"],

['MasterApprenticeSubmissionDocuments','Master Dokumen Pengajuan Magang','Apprentice Submission Document Master','研修生提出書類マスター',
 'Daftar master semua dokumen yang diperlukan dalam proses pengajuan penempatan peserta magang ke Jepang. Digunakan sebagai template checklist untuk setiap peserta.',
 'Master list of all documents required in the Japan apprentice placement submission process. Used as a checklist template for each apprentice.',
 '日本研修生配置申請プロセスで必要な全書類のマスターリスト。各研修生のチェックリストテンプレートとして使用します。',
 ['View all required submission documents','Add new document to the master list','Set as mandatory or optional','Assign to document category','Referenced when tracking per-apprentice document status'],
 "A[Master List Defined] --> B[Applied to Each Apprentice]\n    B --> C[Track Per-Apprentice Status]\n    C --> D[All Docs Complete = Ready for Japan]"],

['MasterRejectedReasons','Master Alasan Penolakan','Rejection Reason Master','不合格理由マスター',
 'Data referensi alasan penolakan kandidat atau peserta dari program: tidak memenuhi syarat fisik, gagal wawancara, dokumen tidak lengkap, mengundurkan diri, dll.',
 'Reference data for rejection reasons for candidates or trainees from the program: does not meet physical requirements, failed interview, incomplete documents, withdrew, etc.',
 'プログラムからの候補者・研修生の不合格理由の参照データ（身体条件未達、面接不合格、書類不備、辞退など）。',
 ['View rejection reason options','Add new reason','Used when marking candidates/trainees as rejected','Enables analysis of why applicants are rejected','Helps identify and address common rejection causes'],
 "A[Candidate/Trainee Rejected] --> B[Select Rejection Reason]\n    B --> C[Reason Master Used]\n    C --> D[Stored in Record]\n    D --> E[Used for Trend Analysis]"],

['MasterTrainingCompetencies','Master Kompetensi Pelatihan','Training Competency Master','研修コンピテンシーマスター',
 'Data referensi kompetensi yang harus dicapai dalam program pelatihan pra-keberangkatan: bahasa Jepang level N5, teknis industri, budaya kerja Jepang, keselamatan kerja, dll.',
 'Reference data for competencies to be achieved in the pre-departure training program: Japanese N5 level, industry technical skills, Japanese work culture, workplace safety, etc.',
 '出発前研修プログラムで達成すべきコンピテンシーの参照データ（日本語N5レベル、産業技術スキル、日本の職場文化、安全衛生など）。',
 ['View all training competencies','Add new competency with description and level','Link competencies to training courses','Used to evaluate if trainee is ready for Japan','Competency achievement tracked per trainee'],
 "A[Training Program Defined] --> B[Competencies Listed]\n    B --> C[Trainee Trained per Competency]\n    C --> D[Competency Assessed]\n    D --> E{Achieved?}\n    E -->|Yes| F[Trainee Qualified]\n    E -->|No| G[Additional Training]"],

['MasterTrainingTestScoreGrades','Master Grade Nilai Ujian Pelatihan','Training Test Score Grade Master','研修テストスコアグレードマスター',
 'Data referensi grade atau predikat nilai ujian pelatihan: A (Sangat Baik), B (Baik), C (Cukup), D (Kurang), E (Gagal). Digunakan dalam penilaian hasil tes peserta pelatihan.',
 'Reference data for training test score grades: A (Excellent), B (Good), C (Fair), D (Poor), E (Fail). Used in evaluating trainee test results.',
 '研修テストスコアグレードの参照データ（A：優秀、B：良好、C：普通、D：不十分、E：不合格）。研修生のテスト結果評価に使用します。',
 ['View grade scale options','Add or modify grade thresholds','Used to classify test scores','Consistent grading across all test types','Grade trends help evaluate training program quality'],
 "A[Test Score Recorded] --> B[Grade Calculated]\n    B --> C[Grade Master Referenced]\n    C --> D[Grade Displayed on Record]\n    D --> E[Used in Score Average]"],

['MasterEmployeeStatuses','Master Status Karyawan','Employee Status Master','従業員状況マスター',
 'Data referensi status karyawan yang digunakan untuk mengklasifikasikan staf internal TMM atau peserta magang setelah kembali: aktif, non-aktif, pensiun, kontrak, dll.',
 'Reference data for employee statuses used to classify internal TMM staff or apprentices after return: active, inactive, retired, contract, etc.',
 'TMM内部スタッフまたは帰国後の研修生を分類するための従業員状況の参照データ（アクティブ、非アクティブ、退職、契約など）。',
 ['View employee status options','Add or edit status values','Used in user/staff management forms','Affects access rights and system roles','Status history tracks career progression'],
 "A[Staff/Apprentice Added] --> B[Status Assigned]\n    B --> C[Status Master Used]\n    C --> D[Access & Record Based on Status]"],

['MasterEmploymentPositions','Master Posisi Jabatan','Employment Position Master','役職マスター',
 'Data referensi posisi atau jabatan pekerjaan yang digunakan dalam profil pengalaman kerja dan penempatan peserta di organisasi penerima.',
 'Reference data for employment positions used in work experience profiles and apprentice placement at acceptance organizations.',
 '職歴プロフィールと受け入れ機関での研修生配置に使用される役職の参照データ。',
 ['View all position options','Add new position type','Used in work experience and placement forms','Helps categorize work levels','Important for matching skills to Japan position requirements'],
 "A[Work Experience/Placement Form] --> B[Position Selected]\n    B --> C[Position Master]\n    C --> D[Saved to Work Record]"],

['MasterIcons','Master Ikon','Icon Master','アイコンマスター',
 'Daftar ikon Font Awesome yang tersedia untuk digunakan dalam menu navigasi dan antarmuka sistem TMM. Membantu administrator memilih ikon yang tepat saat membuat atau mengedit menu.',
 'List of available Font Awesome icons for use in navigation menus and the TMM system interface. Helps administrators choose appropriate icons when creating or editing menu items.',
 'ナビゲーションメニューとTMMシステムインターフェースで使用可能なFont Awesomeアイコンの一覧。管理者がメニュー項目を作成・編集する際に適切なアイコンを選択するのに役立ちます。',
 ['Browse available icons','Search by icon name or category','Preview icon appearance','Copy icon class name for use in menu management','Reference when assigning icons to new menu items'],
 "A[Admin Opens Menu Management] --> B[Needs to Pick Icon]\n    B --> C[Opens Icon Master]\n    C --> D[Selects Appropriate Icon]\n    D --> E[Icon Class Used in Menu Definition]"],

['MasterTranslations','Master Terjemahan','Translation Master','翻訳マスター',
 'Mengelola terjemahan label, teks, dan konten antarmuka sistem TMM dalam tiga bahasa: Indonesia, Inggris, dan Jepang. Digunakan untuk mendukung tampilan multi-bahasa sistem.',
 'Manages translations for UI labels, text, and content in the TMM system across three languages: Indonesian, English, and Japanese. Supports the multi-language display of the system.',
 'TMMシステムのUIラベル・テキスト・コンテンツの3言語（インドネシア語、英語、日本語）翻訳を管理します。システムの多言語表示をサポートします。',
 ['View all translation entries','Add new translation key with values in all 3 languages','Edit existing translations','Used by the language switching feature across the system','Keep all 3 languages in sync when adding new features'],
 "A[New UI Text Needed] --> B[Add to Translation Master]\n    B --> C[Enter ID / EN / JP Values]\n    C --> D[System Displays Correct Language]\n    D --> E[User Switches Language Seamlessly]"],

// --- SYSTEM ---
['Dashboard','Dashboard Utama','Main Dashboard','メインダッシュボード',
 'Halaman ringkasan utama sistem TMM yang menampilkan statistik kunci: jumlah kandidat aktif, peserta pelatihan, peserta magang di Jepang, dan peserta yang kembali. Tampilan grafik distribusi per LPK, per negara bagian, dan per perusahaan penerima.',
 'The main TMM system summary page showing key statistics: active candidates, trainees in training, apprentices currently in Japan, and returned apprentices. Charts show distribution by LPK, region, and acceptance organization.',
 'TMMシステムのメインサマリーページ。アクティブ候補者、研修中の研修生、日本で就労中のアプレンティス、帰国済みアプレンティスの主要統計を表示。LPK、地域、受け入れ機関別の分布グラフも表示します。',
 ['View real-time count of candidates, trainees, and apprentices','See distribution charts by LPK, region, and Japan location','Monitor pending document issues and action items','Quick links to common management tasks','Export summary report for management review'],
 "A[User Logs In] --> B[Dashboard Loaded]\n    B --> C[Candidates Count]\n    B --> D[Trainees Count]\n    B --> E[Apprentices in Japan Count]\n    B --> F[Returned Count]\n    C & D & E & F --> G[Overview Summary]"],

['Users','Manajemen Pengguna','User Management','ユーザー管理',
 'Mengelola akun pengguna sistem TMM: nama, email, username, peran (role), dan status aktif. Administrator dapat menambah, mengedit, dan menonaktifkan akun pengguna dari halaman ini.',
 'Manages TMM system user accounts: name, email, username, role assignment, and active status. Administrators can add, edit, and deactivate user accounts from this page.',
 'TMMシステムのユーザーアカウント（氏名、メール、ユーザー名、役割割り当て、有効状態）を管理します。管理者はこのページからユーザーアカウントの追加、編集、無効化ができます。',
 ['View all user accounts with their roles','Add new user with email and password','Assign role (Admin, Manager, Operator, etc.)','Deactivate user account without deleting data','Reset password for locked-out users'],
 "A[New Staff Joins] --> B[Admin Creates Account]\n    B --> C[Assigns Username & Password]\n    C --> D[Assigns Role]\n    D --> E[User Can Log In]\n    E --> F[Access Based on Role Permissions]"],

['Roles','Manajemen Peran','Role Management','役割管理',
 'Mengelola peran pengguna dalam sistem TMM: nama peran, deskripsi, dan hak akses yang terkait. Setiap peran menentukan menu dan fitur apa yang dapat diakses oleh pengguna dengan peran tersebut.',
 'Manages user roles in the TMM system: role name, description, and associated access rights. Each role determines which menus and features users with that role can access.',
 'TMMシステムのユーザー役割（役割名、説明、関連するアクセス権）を管理します。各役割は、その役割を持つユーザーがアクセスできるメニューと機能を決定します。',
 ['View all defined roles','Add new role with name and description','Edit role name or description','Delete unused roles','Go to Permissions page to assign menus to roles'],
 "A[Role Created] --> B[Name & Description Set]\n    B --> C[Menu Permissions Assigned via Permissions Page]\n    C --> D[Users Assigned this Role]\n    D --> E[Users Get Access Defined by Role]"],

['UserRoles','Penugasan Peran Pengguna','User Role Assignments','ユーザー役割割り当て',
 'Mengelola penugasan peran kepada pengguna. Satu pengguna dapat memiliki lebih dari satu peran. Halaman ini menampilkan daftar penugasan pengguna ke peran yang ada.',
 'Manages the assignment of roles to users. A single user can have multiple roles. This page shows the list of user-to-role assignments.',
 'ユーザーへの役割割り当てを管理します。1人のユーザーが複数の役割を持つことができます。このページはユーザーと役割の割り当て一覧を表示します。',
 ['View all user-role assignments','Add new role assignment for a user','Remove a role from a user','A user with multiple roles inherits all permissions from each role','Check effective permissions using Permissions overview'],
 "A[Admin Assigns Role to User] --> B[User-Role Record Created]\n    B --> C[User Now Has Role Permissions]\n    C --> D[Multiple Roles Merge Permissions]\n    D --> E[User Accesses Allowed Menus]"],

['Sessions','Sesi Login','Login Sessions','ログインセッション',
 'Menampilkan sesi login aktif dan riwayat sesi pengguna sistem TMM. Administrator dapat memantau siapa yang sedang login dan mengakhiri sesi yang mencurigakan.',
 'Shows active login sessions and session history for TMM system users. Administrators can monitor who is currently logged in and terminate suspicious sessions.',
 'TMMシステムユーザーのアクティブなログインセッションとセッション履歴を表示します。管理者は現在ログイン中のユーザーを監視し、不審なセッションを終了できます。',
 ['View all active sessions with user, IP, and time','Identify suspicious concurrent logins','Terminate a session if needed','Review session history for audit purposes','Monitor login patterns for security'],
 "A[User Logs In] --> B[Session Created]\n    B --> C[Admin Can See Session]\n    C --> D{Suspicious?}\n    D -->|Yes| E[Admin Terminates Session]\n    D -->|No| F[Session Expires Normally]"],

['Files','Manajemen File','File Management','ファイル管理',
 'Mengelola file dan lampiran yang diunggah ke sistem TMM, termasuk foto, scan dokumen, dan file pendukung lainnya. Memantau penggunaan penyimpanan dan mengelola file yang tidak terpakai.',
 'Manages files and attachments uploaded to the TMM system, including photos, document scans, and other supporting files. Monitors storage usage and manages unused files.',
 'TMMシステムにアップロードされたファイル・添付ファイル（写真、書類スキャン、その他サポートファイル）を管理します。ストレージ使用量の監視と未使用ファイルの管理を行います。',
 ['View all uploaded files with size and uploader','Filter by file type or module','Preview or download files','Delete unused files to free storage','Track storage usage by module'],
 "A[User Uploads File] --> B[File Stored on Server]\n    B --> C[File Record Created]\n    C --> D[Linked to Entity (Candidate/Doc/etc.)]\n    D --> E[Admin Can View & Manage All Files]"],

['Trainings','Manajemen Program Pelatihan','Training Program Management','研修プログラム管理',
 'Mengelola program pelatihan yang diselenggarakan untuk peserta magang TMM: nama program, durasi, kurikulum, LPK penyelenggara, dan batch yang mengikuti program ini.',
 'Manages training programs conducted for TMM apprentices: program name, duration, curriculum, organizing LPK, and batches enrolled in the program.',
 'TMM研修生向けに実施される研修プログラム（プログラム名、期間、カリキュラム、担当LPK、参加バッチ）を管理します。',
 ['View all training programs','Add new program with name, duration, and curriculum','Assign LPK to conduct the training','Link training batches to the program','Track program outcomes and graduate counts'],
 "A[New Training Program Created] --> B[LPK Assigned]\n    B --> C[Trainees Enrolled via Batch]\n    C --> D[Training Conducted]\n    D --> E[Results Recorded]\n    E --> F[Graduates Promoted to Apprentice Stage]"],

// --- ADMIN ---
['Admin/LpkRegistration','Registrasi LPK (Admin)','LPK Registration (Admin)','LPK登録（管理者）',
 'Halaman administrasi untuk memproses dan menyetujui pendaftaran Lembaga Pelatihan Kerja (LPK) baru yang ingin bermitra dengan TMM. Administrator meninjau, memverifikasi, dan menyetujui permohonan LPK.',
 'Administrative page for processing and approving new Vocational Training Institution (LPK) registrations that want to partner with TMM. Admin reviews, verifies, and approves LPK applications.',
 'TMM とのパートナーシップを希望する新規職業訓練機関（LPK）の登録申請を処理・承認する管理ページ。管理者がLPK申請を審査、確認、承認します。',
 ['View all pending LPK registration applications','Review submitted documents and credentials','Verify LPK accreditation and facilities','Approve or reject applications','Approved LPKs become partner institutions in TMM'],
 "A[LPK Submits Application] --> B[Admin Reviews Application]\n    B --> C[Verify Documents & Credentials]\n    C --> D{Approved?}\n    D -->|Yes| E[LPK Added as Partner]\n    D -->|No| F[Rejection with Reasons Sent]"],

['Admin/StakeholderDashboard','Dashboard Pemangku Kepentingan (Admin)','Stakeholder Dashboard (Admin)','ステークホルダーダッシュボード（管理者）',
 'Tampilan ringkasan khusus administrator yang menampilkan status semua pemangku kepentingan: jumlah LPK aktif, organisasi penerima, asosiasi koperasi, dan lembaga keahlian khusus, beserta statistik penempatan.',
 'Administrator-only summary view showing status of all stakeholders: count of active LPKs, acceptance organizations, cooperative associations, and special skill institutions, with placement statistics.',
 '全ステークホルダーの状況（アクティブなLPK、受け入れ機関、協同組合、特定技能機関の数と配置統計）を表示する管理者専用サマリービュー。',
 ['View all stakeholder counts and statuses','See placement statistics per stakeholder type','Identify inactive or under-performing partners','Export stakeholder report for management','Navigate directly to each stakeholder type management page'],
 "A[Admin Opens Dashboard] --> B[Load All Stakeholder Data]\n    B --> C[Display Counts & Stats]\n    C --> D[Admin Identifies Issues]\n    D --> E[Takes Action on Partners]"],

// --- OTHER ---
['InstitutionRegistration','Registrasi Institusi','Institution Registration','機関登録',
 'Portal bagi lembaga (LPK, organisasi penerima, atau asosiasi koperasi) untuk mendaftarkan diri sebagai mitra TMM. Form pendaftaran mengumpulkan data dasar institusi untuk ditinjau administrator.',
 'Portal for institutions (LPK, acceptance organizations, or cooperative associations) to register as TMM partners. The registration form collects basic institution data for administrator review.',
 'LPK、受け入れ機関、協同組合がTMMパートナーとして登録するためのポータル。登録フォームは管理者審査用の基本機関データを収集します。',
 ['Open registration form for new institution','Fill in institution name, type, and contact information','Upload required credentials and accreditation documents','Submit application for admin review','Track application status after submission'],
 "A[Institution Wants to Partner] --> B[Opens Registration Portal]\n    B --> C[Fills Registration Form]\n    C --> D[Uploads Documents]\n    D --> E[Submits to Admin]\n    E --> F[Admin Reviews & Approves]"],
];

// Shared CSS and template builder
function buildProcessFlow($module) {
    $dir = $module[0];
    $titleInd = $module[1];
    $titleEng = $module[2];
    $titleJpn = $module[3];
    $descInd = $module[4];
    $descEng = $module[5];
    $descJpn = $module[6];
    $steps = $module[7]; // array of step descriptions in English
    $mermaid = $module[8];

    $stepsInd = $steps;
    $stepsJpn = $steps;

    $stepsHtml = '';
    foreach ($steps as $i => $step) {
        $n = $i + 1;
        $stepsHtml .= <<<STEP

        <div class="pf-step">
            <span class="pf-step-num">{$n}</span>
            <div class="pf-step-body">
                <?php if (\$currentLang === 'ind'): ?>
                    {$step}
                <?php elseif (\$currentLang === 'eng'): ?>
                    {$step}
                <?php else: ?>
                    {$step}
                <?php endif; ?>
            </div>
        </div>
STEP;
    }

    $mermaidInd = $mermaid;
    $mermaidEng = $mermaid;
    $mermaidJpn = $mermaid;

    return <<<PHP
<?php
\$currentLang = \$this->request->getSession()->read('Config.language') ?: 'ind';
?>
<style>
.pf-wrapper{max-width:900px;margin:0 auto;padding:20px}
.pf-lang{text-align:center;margin-bottom:24px;padding:18px;background:linear-gradient(135deg,#e0f7fa,#b2ebf2);border-radius:12px;box-shadow:0 4px 16px rgba(0,188,212,.15)}
.lang-btn{display:inline-block;padding:9px 22px;margin:0 6px;border:2px solid rgba(0,188,212,.4);background:rgba(255,255,255,.75);color:#00796b;border-radius:22px;cursor:pointer;transition:.25s;text-decoration:none;font-weight:600;font-size:14px}
.lang-btn:hover,.lang-btn.active{background:#fff;color:#00695c;border-color:#00bcd4;text-decoration:none;transform:translateY(-2px);box-shadow:0 4px 14px rgba(0,188,212,.3)}
.pf-section{background:#fff;border:1px solid #e3f2fd;border-radius:10px;padding:22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
.pf-section h2{margin:0 0 14px;font-size:18px;color:#006064;display:flex;align-items:center;gap:10px}
.pf-desc{background:linear-gradient(135deg,#e0f7fa,#e8f5e9);border-left:4px solid #00bcd4;padding:14px 18px;border-radius:6px;color:#004d40;font-size:15px;line-height:1.6}
.pf-step{display:flex;gap:14px;margin-bottom:14px;align-items:flex-start}
.pf-step-num{min-width:32px;height:32px;background:linear-gradient(135deg,#00bcd4,#00838f);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0}
.pf-step-body{padding-top:5px;color:#37474f;font-size:14px;line-height:1.55}
.mermaid{background:#f8fdff;border:1px solid #b2ebf2;border-radius:8px;padding:16px;overflow-x:auto}
</style>

<div class="pf-wrapper">
    <div class="pf-lang">
        <a href="?lang=ind" class="lang-btn <?= \$currentLang==='ind'?'active':'' ?>">🇮🇩 Indonesian</a>
        <a href="?lang=eng" class="lang-btn <?= \$currentLang==='eng'?'active':'' ?>">🇬🇧 English</a>
        <a href="?lang=jpn" class="lang-btn <?= \$currentLang==='jpn'?'active':'' ?>">🇯🇵 日本語</a>
    </div>

    <div class="pf-section">
        <h2><i class="fas fa-info-circle"></i>
            <?php if(\$currentLang==='ind'): ?>{$titleInd}
            <?php elseif(\$currentLang==='eng'): ?>{$titleEng}
            <?php else: ?>{$titleJpn}<?php endif; ?>
        </h2>
        <div class="pf-desc">
            <?php if(\$currentLang==='ind'): ?>
                {$descInd}
            <?php elseif(\$currentLang==='eng'): ?>
                {$descEng}
            <?php else: ?>
                {$descJpn}
            <?php endif; ?>
        </div>
    </div>

    <div class="pf-section">
        <h2><i class="fas fa-list-ol"></i>
            <?php if(\$currentLang==='ind'): ?>Alur Penggunaan Halaman Utama
            <?php elseif(\$currentLang==='eng'): ?>Main Page Usage Flow
            <?php else: ?>メインページ利用フロー<?php endif; ?>
        </h2>
        {$stepsHtml}
    </div>

    <div class="pf-section">
        <h2><i class="fas fa-project-diagram"></i>
            <?php if(\$currentLang==='ind'): ?>Diagram Alur Visual
            <?php elseif(\$currentLang==='eng'): ?>Visual Process Flow
            <?php else: ?>ビジュアルフロー図<?php endif; ?>
        </h2>
        <div class="mermaid">
graph TD
    {$mermaid}

    style A fill:#e3f2fd
        </div>
    </div>
</div>
PHP;
}

$written = 0;
$skipped = 0;
foreach ($modules as $module) {
    $dir = $module[0];
    $path = $base . '/' . $dir . '/process_flow.ctp';
    if (!file_exists(dirname($path))) {
        echo "SKIP (no dir): $path\n";
        $skipped++;
        continue;
    }
    $content = buildProcessFlow($module);
    file_put_contents($path, $content);
    echo "WROTE: $path\n";
    $written++;
}
echo "\nDone: $written written, $skipped skipped.\n";
