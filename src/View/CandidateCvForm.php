<?php
namespace App\View;

/**
 * The candidate CV form: its labels, and its values already formatted.
 *
 * The CV exists twice over - as the page Element/Candidates/cv_body.ctp
 * prints and as the workbook CandidatesController::exportCvExcel() writes -
 * because a spreadsheet cannot render a template. Two documents means two
 * documents to keep in step, and this application has paid for that mistake
 * more than once: the Excel export was still writing "Basic Information" and
 * "Contact Information" long after the page had become the LPK's own form,
 * and it carried none of 確認事項 at all.
 *
 * This is where the workbook reads its labels and its values from: 有 and 無
 * rather than 1 and 0, the Japanese spelling of a reference row in preference
 * to the Indonesian, the date written 2007年9月29日, the address assembled
 * from its four region rows.
 *
 * The page does not read from here yet - cv_body.ctp still carries its own
 * copy of the same labels and the same formatting, and moving it across is
 * the next step. Until then what holds the two together is
 * tests/candidate_cv_excel.php, which takes every label this class declares
 * and requires it in the rendered page as well as in the workbook. That is a
 * weaker guarantee than one source, and it is written down here rather than
 * left to be discovered.
 *
 * It takes an entity, saved or not - the registration wizard builds one in
 * memory from what is in the session - so no association may be assumed to
 * have been loaded. Every one is reached through has().
 */
class CandidateCvForm
{
    /**
     * @var \Cake\Datasource\EntityInterface
     */
    protected $candidate;

    /**
     * @param \Cake\Datasource\EntityInterface $candidate The candidate.
     */
    public function __construct($candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Every label on the form: key => [Japanese, Indonesian].
     *
     * The Indonesian goes through __() where the form has one; a label that
     * is Japanese only, such as 研修生No., has an empty second half.
     *
     * @return array
     */
    public function labels()
    {
        return [
            'trainee_no' => ['研修生No.', ''],
            'gender' => ['性別', 'Jenis Kelamin'],
            'name' => ['氏名', 'Nama'],
            'age' => ['年齢', 'Usia'],
            'height' => ['身長', 'Tinggi Badan'],
            'weight' => ['体重', 'Berat Badan'],
            'birth_date' => ['生年月日', 'Tanggal Lahir'],
            'blood_type' => ['血液型', 'Golongan Darah'],
            'marriage_status' => ['配偶者', 'Status Pernikahan'],
            'birth_place' => ['出身地', 'Tempat Lahir'],
            'religion' => ['宗教', 'Agama'],
            'been_to_japan' => ['訪日経験', 'Pernah ke Jepang'],
            'passport' => ['旅券の有無', 'Pernah Memiliki Paspor'],
            'notes' => ['備考', 'Catatan'],
            'address' => ['現住所', 'Alamat Rumah'],
            'phone' => ['No.HP（携帯電話番号）', ''],
            'confirmation' => ['確認事項', 'Poin konfirmasi'],
            'strengths' => ['長所', 'Kelebihan'],
            'weaknesses' => ['短所', 'Kekurangan'],
            'hobby' => ['趣味', 'Hobi'],
            'eyesight' => ['視力', 'Penglihatan (minus di)'],
            'colour_blind' => ['色覚異常', 'Buta warna/tidak'],
            'handed' => ['利き手', 'Tangan yang dominan'],
            'smoking' => ['喫煙', 'Merokok/tidak'],
            'tattoo' => ['刺青', 'Bertato/tidak'],
            'alcohol' => ['飲酒', 'Minum alkohol/tidak'],
            'salary' => ['現在の収入', 'Jumlah pendapatan terakhir'],
            'saving_goal' => ['3年間日本での貯金目標', 'Target tabungan 3 thn di Jepang'],
            'purpose' => ['技能実習に行く目的', 'Tujuan magang ke Jepang'],
            'return_wish' => ['実習3年間終了後日本に戻って、働く希望',
                'Selesai 3 tahun magang, ada/tidak keinginan kembali bekerja ke Jepang'],
            'return_as' => ['「有」の場合、何をしたい？', 'Jika jawab "Ya", sebagai apa?'],
            'education' => ['学歴', 'Pendidikan'],
            'period_years' => ['期間年', 'Tahun'],
            'school' => ['学校名', 'Nama Sekolah'],
            'major' => ['専攻', 'Jurusan'],
            'work' => ['職歴', 'Pengalaman kerja'],
            'period' => ['期間', 'Tahun'],
            'company' => ['会社名', 'Nama Perusahaan'],
            'job_kind' => ['職種', 'Jenis kerja'],
            'family' => ['家族構成', 'Susunan Keluarga Kandung'],
            'relationship' => ['続柄', 'Urutan Keluarga'],
            'member' => ['氏名', 'Nama Anggota Keluarga'],
            'member_age' => ['年齢', 'Usia'],
            'occupation' => ['職業', 'Pekerjaan'],
            'extras' => ['資格・研修', 'Sertifikat & Pelatihan'],
            'obtained' => ['取得日', 'Tanggal'],
            'extra_name' => ['資格名・コース名', 'Nama Sertifikat / Kursus'],
            'issuer' => ['取得機関', 'Lembaga'],
        ];
    }

    /**
     * One label, Japanese and Indonesian on one line as the head block prints
     * them.
     *
     * @param string $key A key of labels().
     * @return string
     */
    public function label($key)
    {
        $labels = $this->labels();
        if (!isset($labels[$key])) {
            return '';
        }
        list($ja, $id) = $labels[$key];

        return trim($ja . ' ' . ($id === '' ? '' : __($id)));
    }

    /**
     * Every value on the form, formatted, keyed as labels() is.
     *
     * @return array
     */
    public function values()
    {
        $candidate = $this->candidate;
        $company = $this->companyRow();

        return [
            // Left empty on purpose: the trainee number is the receiving
            // side's to give once they accept the candidate.
            'trainee_no' => '',
            'gender' => $this->reference($this->related($candidate, 'master_gender')),
            'name' => (string)$candidate->get('name'),
            'name_katakana' => (string)$candidate->get('name_katakana'),
            'age' => $this->age(),
            'height' => $this->number($candidate->get('body_height')),
            'weight' => $this->number($candidate->get('body_weight')),
            'birth_date' => $this->japaneseDate($candidate->get('birth_date')),
            'blood_type' => $this->reference($this->related($candidate, 'master_blood_type')),
            'marriage_status' => $this->reference($this->related($candidate, 'master_marriage_status')),
            'birth_place' => (string)$candidate->get('birth_place'),
            'birth_place_katakana' => (string)$candidate->get('birth_place_katakana'),
            'religion' => $this->reference($this->related($candidate, 'master_religion')),
            'been_to_japan' => $this->yesNo($candidate->get('is_ever_went_to_japan')),
            'passport' => $this->yesNo($candidate->get('is_holding_passport')),
            'address' => $this->address(),
            'phone' => (string)$candidate->get('telephone_mobile'),
            'company' => $this->reference($company, ['title']),
            'strengths' => (string)$candidate->get('strengths'),
            'weaknesses' => (string)$candidate->get('weaknesses'),
            'hobby' => (string)$candidate->get('hobby'),
            'eyesight' => $this->yesNo($candidate->get('is_wear_eye_glasses')),
            'eyesight_detail' => (string)$candidate->get('explain_eye_condition'),
            'colour_blind' => $this->yesNo($candidate->get('is_color_blind')),
            'colour_blind_detail' => (string)$candidate->get('explain_color_blind'),
            'handed' => $this->yesNo($candidate->get('is_right_handed'), '右', '左'),
            'smoking' => $this->yesNo($candidate->get('is_smoking'), '喫煙', '禁煙'),
            'tattoo' => $this->yesNo($candidate->get('is_tattooed')),
            'alcohol' => $this->yesNo($candidate->get('is_drinking_alcohol')),
            'salary' => $this->money($candidate->get('last_salary_amount')),
            'saving_goal' => $this->money($candidate->get('saving_goal_amount')),
            'purpose' => (string)$candidate->get('application_reasons'),
            'return_wish' => $this->yesNo($candidate->get('will_go_to_japan_after_finished')),
            'return_as' => (string)$candidate->get('expected_work_upon_returning_to_japan'),
        ];
    }

    /**
     * One value.
     *
     * @param string $key A key of values().
     * @return string
     */
    public function value($key)
    {
        $values = $this->values();

        return isset($values[$key]) ? $values[$key] : '';
    }

    /**
     * The letterhead lines, top to bottom.
     *
     * The operator's own, not the candidate's institution: a CV goes to a
     * receiving company from the sending organisation that holds the licence.
     *
     * @return array
     */
    public function letterhead()
    {
        return [
            'LPK TRIMITRA MATAHARI MANDIRI',
            'Ruko Kingswood Unit 1 - 3',
            'Jl. Raya Jatibening No. 40 – Bekasi Jawa Barat 17412',
            'Telp : (021) 84988942',
            'Email: lpktrimitramandiri@yahoo.com',
        ];
    }

    /**
     * The two lines naming the employer.
     *
     * @return array
     */
    public function company()
    {
        $name = $this->value('company');

        return ['企業名：' . $name, __('Nama Calon Perusahaan') . ': ' . $name];
    }

    /**
     * The photo, when the file is really on this server.
     *
     * Several records name a photo that was never uploaded.
     *
     * @return string|null An absolute path, or null.
     */
    public function photoPath()
    {
        $photo = $this->candidate->get('image_photo');
        if (empty($photo)) {
            return null;
        }
        $path = WWW_ROOT . $photo;

        return file_exists($path) ? $path : null;
    }

    /**
     * The nine lines of the head block, as the form orders them.
     *
     * Each is the left column (a label on its own line, or the value centred
     * under it), then the Japanese label, the Indonesian label, the value and
     * its unit.
     *
     * @return array
     */
    public function head()
    {
        $v = $this->values();
        $labels = $this->labels();
        $line = function ($leftKind, $left, $key, $value, $unit = '') use ($labels) {
            return [
                'left_kind' => $leftKind,
                'left' => $left,
                'ja' => $labels[$key][0],
                'id' => $labels[$key][1] === '' ? '' : __($labels[$key][1]),
                'value' => $value,
                'unit' => $unit,
            ];
        };

        return [
            $line('split', $this->label('trainee_no'), 'gender', $v['gender']),
            $line('label', $this->label('name'), 'age', $v['age'], '歳'),
            $line('value', $v['name'], 'height', $v['height'], 'CM'),
            $line('value', $v['name_katakana'], 'weight', $v['weight'], 'KG'),
            $line('label', $this->label('birth_date'), 'blood_type', $v['blood_type'], '型'),
            $line('value', $v['birth_date'], 'marriage_status', $v['marriage_status']),
            $line('label', $this->label('birth_place'), 'religion', $v['religion']),
            $line('value', $v['birth_place'], 'been_to_japan', $v['been_to_japan']),
            $line('value', $v['birth_place_katakana'], 'passport', $v['passport']),
        ];
    }

    /**
     * Everything below the head block, as typed rows a renderer can lay out.
     *
     * @return array
     */
    public function blocks()
    {
        $v = $this->values();
        $out = [];

        $out[] = ['type' => 'section', 'text' => $this->label('address')];
        $out[] = ['type' => 'wide', 'text' => $v['address']];
        $out[] = ['type' => 'pair', 'label' => $this->label('phone'), 'value' => $v['phone']];
        $out[] = ['type' => 'gap'];

        $out[] = ['type' => 'section', 'text' => $this->label('confirmation')];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('strengths'), $v['strengths'],
            $this->label('weaknesses'), $v['weaknesses']]];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('hobby'), $v['hobby'],
            $this->label('eyesight'), $this->join($v['eyesight'], $v['eyesight_detail'])]];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('colour_blind'), $this->join($v['colour_blind'], $v['colour_blind_detail']),
            $this->label('handed'), $v['handed']]];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('smoking'), $v['smoking'],
            $this->label('tattoo'), $v['tattoo']]];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('alcohol'), $v['alcohol'],
            $this->label('salary'), $v['salary']]];
        $out[] = ['type' => 'two', 'cells' => [$this->label('saving_goal'), $v['saving_goal']]];
        $out[] = ['type' => 'two', 'cells' => [$this->label('purpose'), $v['purpose']]];
        $out[] = ['type' => 'four', 'cells' => [
            $this->label('return_wish'), $v['return_wish'],
            $this->label('return_as'), $v['return_as']]];
        $out[] = ['type' => 'gap'];

        $out[] = ['type' => 'section', 'text' => $this->label('education')];
        $out[] = ['type' => 'three', 'head' => true, 'cells' => [
            $this->label('period_years'), $this->label('school'), $this->label('major')]];
        foreach ($this->educations() as $education) {
            $out[] = ['type' => 'three', 'cells' => $education];
        }
        $out[] = ['type' => 'gap'];

        $out[] = ['type' => 'section', 'text' => $this->label('work')];
        $out[] = ['type' => 'three', 'head' => true, 'cells' => [
            $this->label('period'), $this->label('company'), $this->label('job_kind')]];
        foreach ($this->experiences() as $experience) {
            $out[] = ['type' => 'three', 'cells' => $experience];
        }
        $out[] = ['type' => 'gap'];

        $out[] = ['type' => 'section', 'text' => $this->label('family')];
        $out[] = ['type' => 'family', 'head' => true, 'cells' => [
            $this->label('relationship'), $this->label('member'),
            $this->label('member_age'), $this->label('occupation')]];
        foreach ($this->families() as $family) {
            $out[] = ['type' => 'family', 'cells' => $family];
        }

        $extras = $this->extras();
        if ($extras) {
            $out[] = ['type' => 'gap'];
            $out[] = ['type' => 'section', 'text' => $this->label('extras')];
            $out[] = ['type' => 'three', 'head' => true, 'cells' => [
                $this->label('obtained'), $this->label('extra_name'), $this->label('issuer')]];
            foreach ($extras as $extra) {
                $out[] = ['type' => 'three', 'cells' => $extra];
            }
        }

        return $out;
    }

    /**
     * Schooling, newest row last, as [period, school, major].
     *
     * @return array
     */
    public function educations()
    {
        $out = [];
        foreach ($this->rows('candidate_educations') as $education) {
            $major = $education->get('college_major');
            $strata = $this->reference($this->related($education, 'master_strata'),
                ['title_hiragana', 'title']);

            $out[] = [
                $this->period([
                    $this->japaneseMonth($education->get('college_entry_date')),
                    $this->japaneseMonth($education->get('college_graduate_date')),
                ]),
                (string)$education->get('college_name'),
                $this->join((string)$major, $strata),
            ];
        }

        return $out;
    }

    /**
     * Employment, as [period, company, kind of work].
     *
     * @return array
     */
    public function experiences()
    {
        $out = [];
        foreach ($this->rows('candidate_experiences') as $experience) {
            // year(4) columns: the table keeps the year and not the month, so
            // the month the printed form shows is not ours to write.
            $from = $experience->get('employment_start_year');
            $to = $experience->get('employment_end_year');
            $out[] = [
                $this->period([$from ? $from . '年' : '', $to ? $to . '年' : '']),
                (string)$experience->get('company_name'),
                $this->join((string)$experience->get('title'),
                    (string)$experience->get('job_detail')),
            ];
        }

        return $out;
    }

    /**
     * The family, as [tie, name, age, occupation].
     *
     * @return array
     */
    public function families()
    {
        $out = [];
        foreach ($this->rows('candidate_families') as $family) {
            $age = $family->get('age');
            // The occupation row when it resolved, and the free text only when
            // it did not: the wizard writes the occupation into the detail
            // line as well, for the installations whose master_occupations
            // does not carry what was typed.
            $occupation = $this->reference($this->related($family, 'master_occupation'), ['title']);

            $out[] = [
                $this->reference($this->related($family, 'master_family_connection'),
                    ['title_kanji', 'title']),
                (string)$family->get('name'),
                $age === null || $age === '' ? '' : $age . '歳',
                $occupation !== '' ? $occupation : (string)$family->get('detail'),
            ];
        }

        return $out;
    }

    /**
     * Certificates and courses, as [when, what, who from].
     *
     * The printed form has no row for these and the wizard collects them, so
     * they are printed after it rather than thrown away - and only when there
     * are any, so a candidate with neither gets exactly the form.
     *
     * @return array
     */
    public function extras()
    {
        $out = [];
        foreach ($this->rows('candidate_certifications') as $certification) {
            $out[] = [
                $this->japaneseMonth($certification->get('certification_date')),
                $this->join((string)$certification->get('title'),
                    (string)$certification->get('detail')),
                (string)$certification->get('institution_name'),
            ];
        }
        foreach ($this->rows('candidate_courses') as $course) {
            $year = $course->get('course_year');
            $out[] = [
                $year ? $year . '年' : '',
                $this->join((string)$course->get('title'), (string)$course->get('detail')),
                '',
            ];
        }

        return $out;
    }

    /**
     * The whole form as plain lines, for the fallback that has no spreadsheet
     * library to draw it with.
     *
     * @return array
     */
    public function flat()
    {
        $out = [];
        foreach ($this->letterhead() as $line) {
            $out[] = [$line];
        }
        $out[] = [];
        foreach ($this->company() as $line) {
            $out[] = [$line];
        }
        $out[] = [];
        foreach ($this->head() as $line) {
            $out[] = [$line['left'], trim($line['ja'] . ' ' . $line['id']),
                trim($line['value'] . ' ' . $line['unit'])];
        }
        foreach ($this->blocks() as $block) {
            switch ($block['type']) {
                case 'section':
                case 'wide':
                    $out[] = [$block['text']];
                    break;
                case 'pair':
                    $out[] = [$block['label'], $block['value']];
                    break;
                case 'four':
                case 'two':
                case 'three':
                case 'family':
                    $out[] = $block['cells'];
                    break;
                default:
                    $out[] = [];
            }
        }

        return $out;
    }

    /**
     * The employer the candidate is put forward to.
     *
     * On the candidate when one has been chosen for them, and otherwise on the
     * order they were entered against, which names the same kind of row.
     *
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function companyRow()
    {
        $company = $this->related($this->candidate, 'acceptance_organization');
        if ($company) {
            return $company;
        }
        $order = $this->related($this->candidate, 'apprentice_order');

        return $order ? $this->related($order, 'acceptance_organization') : null;
    }

    /**
     * An association, or null when it was never loaded.
     *
     * @param \Cake\Datasource\EntityInterface|null $entity The owner.
     * @param string $property The association property.
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function related($entity, $property)
    {
        if (!$entity || !$entity->has($property)) {
            return null;
        }

        return $entity->get($property);
    }

    /**
     * The rows of a has-many, or none.
     *
     * @param string $property The association property.
     * @return array
     */
    protected function rows($property)
    {
        $rows = $this->related($this->candidate, $property);

        return is_array($rows) ? $rows : [];
    }

    /**
     * A reference row's label, Japanese first.
     *
     * @param \Cake\Datasource\EntityInterface|null $row The master row.
     * @param array $fields The columns to try, in order.
     * @return string
     */
    protected function reference($row, array $fields = ['title_jpg', 'title'])
    {
        if (!$row) {
            return '';
        }
        foreach ($fields as $field) {
            $value = $row->get($field);
            if ($value !== null && $value !== '') {
                return (string)$value;
            }
        }

        return '';
    }

    /**
     * 有 / 無, and a blank for a flag nobody has answered yet.
     *
     * null is not "no". A candidate whose passport question was never asked
     * must not print as a candidate who has no passport.
     *
     * @param mixed $flag The flag.
     * @param string $yes What a yes reads as.
     * @param string $no What a no reads as.
     * @return string
     */
    protected function yesNo($flag, $yes = '有', $no = '無')
    {
        if ($flag === null || $flag === '') {
            return '';
        }

        return $flag ? $yes : $no;
    }

    /**
     * Rp700,000.
     *
     * @param mixed $amount The amount.
     * @return string
     */
    protected function money($amount)
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return 'Rp' . number_format((float)$amount, 0, ',', ',');
    }

    /**
     * 171, not 171.00; 60, not 6.
     *
     * body_height and body_weight are decimal columns holding whole numbers,
     * and trimming trailing zeros off a string with no decimal point in it
     * eats the last digit of every round number.
     *
     * @param mixed $value The number.
     * @return string
     */
    protected function number($value)
    {
        if ($value === null || $value === '') {
            return '';
        }
        $float = (float)$value;
        if ($float == (int)$float) {
            return (string)(int)$float;
        }

        return rtrim(rtrim(number_format($float, 2, '.', ''), '0'), '.');
    }

    /**
     * A date as a DateTime, whatever shape it arrives in.
     *
     * The wizard's entity carries whatever was typed into the form; a saved
     * record carries a date object.
     *
     * @param mixed $value The date.
     * @return \DateTimeInterface|null
     */
    protected function date($value)
    {
        if (empty($value)) {
            return null;
        }
        if (is_object($value) && method_exists($value, 'format')) {
            return $value;
        }
        try {
            return new \DateTime((string)$value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 2007年9月29日 - the form writes dates the Japanese way and nothing else.
     *
     * @param mixed $value The date.
     * @return string
     */
    protected function japaneseDate($value)
    {
        $date = $this->date($value);

        return $date ? $date->format('Y') . '年' . $date->format('n') . '月'
            . $date->format('j') . '日' : '';
    }

    /**
     * 2007年9月.
     *
     * @param mixed $value The date.
     * @return string
     */
    protected function japaneseMonth($value)
    {
        $date = $this->date($value);

        return $date ? $date->format('Y') . '年' . $date->format('n') . '月' : '';
    }

    /**
     * The candidate's age in whole years.
     *
     * @return string
     */
    protected function age()
    {
        $birth = $this->date($this->candidate->get('birth_date'));

        return $birth ? (string)$birth->diff(new \DateTime())->y : '';
    }

    /**
     * Two ends of a period, with no lone dash when only one is known.
     *
     * The wizard asks only when somebody finished, so half of these have one
     * end, and a dash in front of a year reads as a mistake rather than as an
     * unknown.
     *
     * @param array $ends The two ends.
     * @return string
     */
    protected function period(array $ends)
    {
        return implode(' - ', array_filter($ends));
    }

    /**
     * A value and the note under it, as one string for a single cell.
     *
     * @param string $value The value.
     * @param string $note The note, if any.
     * @return string
     */
    protected function join($value, $note)
    {
        $note = (string)$note;

        return $note === '' ? (string)$value : trim($value . "\n" . $note);
    }

    /**
     * JL.KH. AGUS SALIM NO.34 ... KOTA BEKASI JAWA BARAT 〒17112.
     *
     * @return string
     */
    protected function address()
    {
        $parts = [];
        if (!empty($this->candidate->get('address'))) {
            $parts[] = $this->candidate->get('address');
        }
        foreach (['master_kelurahan', 'master_kecamatan', 'master_kabupaten',
            'master_propinsi'] as $property) {
            $region = $this->related($this->candidate, $property);
            if ($region && $region->get('title')) {
                $parts[] = $region->get('title');
            }
        }
        $address = implode(' ', $parts);
        $postCode = $this->candidate->get('post_code');

        return $postCode ? trim($address . ' 〒' . $postCode) : $address;
    }
}
