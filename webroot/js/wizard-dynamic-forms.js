/**
 * Wizard Dynamic Forms - JavaScript for hasMany relationship forms
 * Handles Add More and Remove functionality for Education, Experience, Family, Certifications, and Courses
 */

// Education Forms
function addEducationEntry() {
    const container = document.getElementById('education-entries');
    const index = container.children.length;

    const template = `
        <div class="card mb-3 education-entry" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fa fa-graduation-cap"></i> Education #${index + 1}</h6>
                    ${index > 0 ? `<button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this, 'education')"><i class="fa fa-trash"></i> Remove</button>` : ''}
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Institution Name <span class="text-danger">*</span></label>
                            <input type="text" name="educations[${index}][institution_name]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Education Level</label>
                            <select name="educations[${index}][level]" class="form-control">
                                <option value="">Select Level</option>
                                <option value="SD">SD (Elementary)</option>
                                <option value="SMP">SMP (Junior High)</option>
                                <option value="SMA">SMA (Senior High)</option>
                                <option value="SMK">SMK (Vocational High)</option>
                                <option value="D3">D3 (Diploma)</option>
                                <option value="S1">S1 (Bachelor)</option>
                                <option value="S2">S2 (Master)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Field of Study</label>
                            <input type="text" name="educations[${index}][field_of_study]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Year Graduated</label>
                            <input type="number" name="educations[${index}][year_graduated]" class="form-control" min="1950" max="2030">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
}

// Experience Forms
function addExperienceEntry() {
    const container = document.getElementById('experience-entries');
    const index = container.children.length;

    const template = `
        <div class="card mb-3 experience-entry" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fa fa-briefcase"></i> Experience #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this, 'experience')"><i class="fa fa-trash"></i> Remove</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="experiences[${index}][company_name]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Position</label>
                            <input type="text" name="experiences[${index}][position]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="experiences[${index}][start_date]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="experiences[${index}][end_date]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Job Description</label>
                            <textarea name="experiences[${index}][job_description]" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
}

// Family Forms
function addFamilyEntry() {
    const container = document.getElementById('family-entries');
    const index = container.children.length;

    const template = `
        <div class="card mb-3 family-entry" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fa fa-users"></i> Family Member #${index + 1}</h6>
                    ${index > 1 ? `<button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this, 'family')"><i class="fa fa-trash"></i> Remove</button>` : ''}
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="families[${index}][full_name]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Relationship</label>
                            <select name="families[${index}][relationship]" class="form-control">
                                <option value="">Select Relationship</option>
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Spouse">Spouse</option>
                                <option value="Child">Child</option>
                                <option value="Sibling">Sibling</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" name="families[${index}][age]" class="form-control" min="0" max="150">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" name="families[${index}][occupation]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="tel" name="families[${index}][contact_number]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
}

// Certification Forms
function addCertificationEntry() {
    const container = document.getElementById('certification-entries');
    const index = container.children.length;

    const template = `
        <div class="card mb-3 certification-entry" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fa fa-certificate"></i> Certification #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this, 'certification')"><i class="fa fa-trash"></i> Remove</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Certification Name</label>
                            <input type="text" name="certifications[${index}][certification_name]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Issuing Organization</label>
                            <input type="text" name="certifications[${index}][issuing_organization]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Issue Date</label>
                            <input type="date" name="certifications[${index}][issue_date]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="certifications[${index}][expiry_date]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
}

// Course Forms
function addCourseEntry() {
    const container = document.getElementById('course-entries');
    const index = container.children.length;

    const template = `
        <div class="card mb-3 course-entry" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><i class="fa fa-book"></i> Course #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this, 'course')"><i class="fa fa-trash"></i> Remove</button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="courses[${index}][course_name]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Training Institution</label>
                            <input type="text" name="courses[${index}][training_institution]" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="courses[${index}][start_date]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="courses[${index}][end_date]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Duration (hours)</label>
                            <input type="number" name="courses[${index}][duration_hours]" class="form-control" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
}

// Remove Entry
function removeEntry(button, type) {
    const entry = button.closest(`.${type}-entry`);
    if (confirm('Are you sure you want to remove this entry?')) {
        entry.remove();
        reindexEntries(type);
    }
}

// Reindex entries after removal
function reindexEntries(type) {
    const container = document.getElementById(`${type}-entries`);
    const entries = container.querySelectorAll(`.${type}-entry`);

    entries.forEach((entry, index) => {
        entry.dataset.index = index;
        const header = entry.querySelector('h6');
        const icon = header.querySelector('i').outerHTML;
        const typeName = type.charAt(0).toUpperCase() + type.slice(1);
        header.innerHTML = `${icon} ${typeName} #${index + 1}`;

        // Update all input names
        const inputs = entry.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}
