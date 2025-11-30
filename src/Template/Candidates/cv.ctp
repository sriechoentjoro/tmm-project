<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<div class="candidates cv content">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-0"><i class="fa fa-file-text"></i> Curriculum Vitae</h4>
                        <div>
                            <?= $this->Html->link('<i class="fa fa-file-excel"></i> Export Excel', ['action' => 'exportCvExcel', $candidate->id], ['class' => 'btn btn-success btn-sm', 'escape' => false]) ?>
                            <?= $this->Html->link('<i class="fa fa-file-pdf"></i> Export PDF', ['action' => 'exportCvPdf', $candidate->id], ['class' => 'btn btn-danger btn-sm', 'escape' => false, 'target' => '_blank']) ?>
                            <?= $this->Html->link('<i class="fa fa-arrow-left"></i> Back', ['action' => 'view', $candidate->id], ['class' => 'btn btn-secondary btn-sm', 'escape' => false]) ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Photo -->
                    <?php if (!empty($candidate->image_photo)): ?>
                        <div class="text-center mb-4">
                            <img src="<?= $this->Url->build('/' . h($candidate->image_photo)) ?>" alt="Candidate Photo" style="width: 200px; height: 200px; border: 2px solid #ddd; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <!-- Basic Information -->
                    <h4 class="border-bottom pb-2 mb-3"><i class="fa fa-user"></i> Basic Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Identity Number</th>
                            <td><?= h($candidate->identity_number) ?></td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td><?= h($candidate->name) ?></td>
                        </tr>
                        <?php if (!empty($candidate->name_katakana)): ?>
                        <tr>
                            <th>Name (Katakana)</th>
                            <td><?= h($candidate->name_katakana) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Place of Birth</th>
                            <td><?= h($candidate->birth_place) ?></td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td><?= h($candidate->birth_date) ?></td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td><?= $candidate->master_gender ? h($candidate->master_gender->name) : '' ?></td>
                        </tr>
                        <tr>
                            <th>Religion</th>
                            <td><?= $candidate->master_religion ? h($candidate->master_religion->name) : '' ?></td>
                        </tr>
                        <tr>
                            <th>Marriage Status</th>
                            <td><?= $candidate->master_marriage_status ? h($candidate->master_marriage_status->name) : '' ?></td>
                        </tr>
                        <?php if (!empty($candidate->master_blood_type)): ?>
                        <tr>
                            <th>Blood Type</th>
                            <td><?= h($candidate->master_blood_type->name) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <!-- Contact Information -->
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-phone"></i> Contact Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Mobile Phone</th>
                            <td><?= h($candidate->telephone_mobile) ?></td>
                        </tr>
                        <tr>
                            <th>Emergency Contact</th>
                            <td><?= h($candidate->telephone_emergency) ?></td>
                        </tr>
                        <?php if (!empty($candidate->email)): ?>
                        <tr>
                            <th>Email</th>
                            <td><?= h($candidate->email) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Address</th>
                            <td><?= h($candidate->address) ?></td>
                        </tr>
                        <?php if (!empty($candidate->post_code)): ?>
                        <tr>
                            <th>Postal Code</th>
                            <td><?= h($candidate->post_code) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <!-- Personal Attributes -->
                    <?php if (!empty($candidate->body_weight) || !empty($candidate->body_height)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-heartbeat"></i> Personal Attributes</h4>
                    <table class="table table-bordered">
                        <?php if (!empty($candidate->body_weight)): ?>
                        <tr>
                            <th width="30%">Body Weight</th>
                            <td><?= h($candidate->body_weight) ?> kg</td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($candidate->body_height)): ?>
                        <tr>
                            <th>Body Height</th>
                            <td><?= h($candidate->body_height) ?> cm</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Right Handed</th>
                            <td><?= $candidate->is_right_handed ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <th>Wears Eye Glasses</th>
                            <td><?= $candidate->is_wear_eye_glasses ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <th>Color Blind</th>
                            <td><?= $candidate->is_color_blind ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <th>Smoking</th>
                            <td><?= $candidate->is_smoking ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <th>Drinking Alcohol</th>
                            <td><?= $candidate->is_drinking_alcohol ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <th>Has Tattoo</th>
                            <td><?= $candidate->is_tattooed ? 'Yes' : 'No' ?></td>
                        </tr>
                    </table>
                    <?php endif; ?>

                    <!-- Education History -->
                    <?php if (!empty($candidate->candidate_educations)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-graduation-cap"></i> Education History</h4>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="35%">Institution</th>
                                <th width="15%">Level</th>
                                <th width="30%">Field of Study</th>
                                <th width="15%">Year Graduated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidate->candidate_educations as $index => $edu): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= h($edu->institution_name) ?></td>
                                <td><?= h($edu->level) ?></td>
                                <td><?= h($edu->field_of_study) ?></td>
                                <td><?= h($edu->year_graduated) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- Work Experience -->
                    <?php if (!empty($candidate->candidate_experiences)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-briefcase"></i> Work Experience</h4>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="30%">Company</th>
                                <th width="25%">Position</th>
                                <th width="15%">Start Date</th>
                                <th width="15%">End Date</th>
                                <th width="10%">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidate->candidate_experiences as $index => $exp): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= h($exp->company_name) ?></td>
                                <td><?= h($exp->position) ?></td>
                                <td><?= h($exp->start_date) ?></td>
                                <td><?= h($exp->end_date) ?></td>
                                <td>
                                    <?php
                                    if ($exp->start_date && $exp->end_date) {
                                        $start = new DateTime($exp->start_date);
                                        $end = new DateTime($exp->end_date);
                                        $diff = $start->diff($end);
                                        echo $diff->y . ' years';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- Family Information -->
                    <?php if (!empty($candidate->candidate_families)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-users"></i> Family Information</h4>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="30%">Full Name</th>
                                <th width="20%">Relationship</th>
                                <th width="10%">Age</th>
                                <th width="20%">Occupation</th>
                                <th width="15%">Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidate->candidate_families as $index => $fam): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= h($fam->full_name) ?></td>
                                <td><?= h($fam->relationship) ?></td>
                                <td><?= h($fam->age) ?></td>
                                <td><?= h($fam->occupation) ?></td>
                                <td><?= h($fam->contact_number) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- Certifications -->
                    <?php if (!empty($candidate->candidate_certifications)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-certificate"></i> Certifications</h4>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="35%">Certification Name</th>
                                <th width="30%">Issuing Organization</th>
                                <th width="15%">Issue Date</th>
                                <th width="15%">Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidate->candidate_certifications as $index => $cert): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= h($cert->certification_name) ?></td>
                                <td><?= h($cert->issuing_organization) ?></td>
                                <td><?= h($cert->issue_date) ?></td>
                                <td><?= h($cert->expiry_date) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- Courses & Training -->
                    <?php if (!empty($candidate->candidate_courses)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-book"></i> Courses & Training</h4>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No.</th>
                                <th width="30%">Course Name</th>
                                <th width="25%">Training Institution</th>
                                <th width="15%">Start Date</th>
                                <th width="15%">End Date</th>
                                <th width="10%">Duration (hrs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidate->candidate_courses as $index => $course): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= h($course->course_name) ?></td>
                                <td><?= h($course->training_institution) ?></td>
                                <td><?= h($course->start_date) ?></td>
                                <td><?= h($course->end_date) ?></td>
                                <td><?= h($course->duration_hours) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                    <!-- Additional Information -->
                    <?php if (!empty($candidate->strengths) || !empty($candidate->weaknesses) || !empty($candidate->hobby)): ?>
                    <h4 class="border-bottom pb-2 mb-3 mt-4"><i class="fa fa-info-circle"></i> Additional Information</h4>
                    <table class="table table-bordered">
                        <?php if (!empty($candidate->strengths)): ?>
                        <tr>
                            <th width="30%">Strengths</th>
                            <td><?= h($candidate->strengths) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($candidate->weaknesses)): ?>
                        <tr>
                            <th>Weaknesses</th>
                            <td><?= h($candidate->weaknesses) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($candidate->hobby)): ?>
                        <tr>
                            <th>Hobby</th>
                            <td><?= h($candidate->hobby) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .card-header .btn {
        display: none;
</style>

