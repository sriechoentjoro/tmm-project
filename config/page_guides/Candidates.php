<?php
/**
 * Guide for the candidate register, including the promotion to trainee.
 */

return [
    'icon' => 'fa-user-plus',
    'title' => __('Candidates'),
    'subtitle' => __('The people an institution proposes, and everything selection needs to decide about them.'),
    'lead' => __('A candidate is a person an institution has found in answer to an apprentice order. This screen is where their profile is built up and where three kinds of result accumulate against them: a physical test score, an interview score from the accepting company, and a medical check-up. When those are in, recruitment decides who goes forward, and a candidate who goes forward becomes a trainee - the same person, carried over with their whole profile, into the training phase.'),

    'actors' => [
        ['role' => 'lpk-penyangga', 'can' => __('Registers and maintains its own candidates. It sees only its own; the filter is applied in the controller.')],
        ['role' => 'tmm-recruitment', 'can' => __('Reads every candidate, records interview results, and promotes candidates to trainee.')],
        ['role' => 'administrator', 'can' => __('Everything, without restriction.')],
    ],

    'before' => [
        ['note' => __('A candidate is proposed against an order that was shared with the institution. Without a shared order there is nothing to recruit for.'), 'url' => '/apprentice-orders', 'label' => __('Apprentice Orders')],
    ],

    'steps' => [
        [
            'title' => __('Register the candidate'),
            'who' => __('The institution'),
            'do' => __('Enter the person: identity, family, education, experience, certifications and courses. The wizard walks through it in order; the plain form is there when you already know what you are doing.'),
            'result' => __('The candidate exists and can be tested, interviewed and examined.'),
            'screen' => ['/candidates/add', __('New Candidate')],
            'data' => 'candidates',
        ],
        [
            'title' => __('Collect the documents'),
            'who' => __('The institution'),
            'do' => __('The submission checklist shows which required documents are in and which are missing.'),
            'result' => __('Document completeness becomes visible, and it is shown again on the promotion screen.'),
            'screen' => ['/candidate-documents/submission-checklist', __('Document checklist')],
        ],
        [
            'title' => __('Record the three results'),
            'who' => __('The institution and recruitment'),
            'do' => __('The physical test is scored by the institution, the interview by recruitment on behalf of the accepting company, and the medical check-up is recorded as its own document. Each writes its score back onto the candidate so it can be read at a glance.'),
            'result' => __('The candidate carries a fitness score, an interview score with a recommendation, and medical records.'),
            'screen' => ['/lpk-candidate-scoring', __('Candidate scoring board')],
            'data' => 'fitness_score, interview_score, interview_recommendation',
        ],
        [
            'title' => __('Declare the candidate through selection'),
            'who' => __('The proposing institution'),
            'do' => __('When the tests, the interview and the documents are done, put the candidate forward from their own page. Nothing is copied and nothing is decided - you are saying the candidate has passed your selection.'),
            'result' => __('The candidate appears on recruitment\'s promotion list. Until somebody puts them forward, recruitment does not see them at all.'),
            'data' => 'lpk_proposed_at',
            'note' => __('A candidate whose medical check-up is marked not fit cannot be put forward, and the button says so rather than failing quietly.'),
        ],
        [
            'title' => __('Promote to trainee'),
            'who' => __('Recruitment staff, or an administrator'),
            'do' => __('The promotion screen lists the candidates institutions have put forward, with their scores and document completeness side by side. Promoting one creates a trainee record and copies the whole profile across - education, experience, certifications, courses and family. Whether a candidate is promoted is recruitment\'s decision alone; the institution only proposes.'),
            'result' => __('The person becomes a trainee with a new TMM code, and the training phase can begin. The candidate record stays where it is, marked as passed.'),
            'screen' => ['/candidates/promote-to-trainee', __('Promote to Trainee')],
            'data' => 'is_candidate_pass = 1, trainees.candidate_id',
            'note' => __('Promoting is refused for anyone who is already a trainee, so a double click cannot create two of the same person.'),
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Order shared with the LPK') . "] --> B[" . __('LPK registers the candidate') . "]\n"
        . "    B --> C[" . __('Physical test') . "]\n"
        . "    B --> D[" . __('AO interview') . "]\n"
        . "    B --> E[" . __('Medical check-up') . "]\n"
        . "    B --> F[" . __('Documents') . "]\n"
        . "    C --> G[" . __('Scoring board') . "]\n"
        . "    D --> G\n"
        . "    E --> G\n"
        . "    F --> G\n"
        . "    G --> P[" . __('LPK declares passed selection') . "]\n"
        . "    P --> H[" . __('Recruitment promotes') . "]\n"
        . "    H --> I[" . __('Trainee created, profile copied') . "]\n"
        . "    style B fill:#e3f2fd\n"
        . "    style I fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-dumbbell',
            'what' => __('The physical test screen writes the fitness score straight onto the candidate, which is why it appears here without being typed twice.'),
            'url' => '/lpk-physical-tests',
            'label' => __('Physical Tests'),
        ],
        [
            'icon' => 'fa-comments',
            'what' => __('The interview screen mirrors its five sub-scores, the overall result and the recommendation onto the candidate.'),
            'url' => '/candidate-record-interviews',
            'label' => __('Interviews'),
        ],
        [
            'icon' => 'fa-user-graduate',
            'what' => __('Promotion creates the trainee. From that point the person is worked on in the training screens, and the candidate record becomes history.'),
            'url' => '/trainees',
            'label' => __('Trainees'),
        ],
        [
            'icon' => 'fa-stethoscope',
            'what' => __('A medical check-up decides the candidate\'s fitness automatically, and a failing one holds them back here. What each result type means is set once on its master screen.'),
            'url' => '/master-medical-check-up-results',
            'label' => __('MCU result types'),
        ],
        [
            'icon' => 'fa-chart-pie',
            'what' => __('Candidate counts by state feed the dashboards and the recruitment reports.'),
            'url' => '/reports',
            'label' => __('Reports'),
        ],
    ],

    'cautions' => [
        __('An empty promotion list usually means nobody has been put forward yet, not that something is broken. A candidate reaches recruitment only when their own institution declares them through selection.'),
        __('Promotion copies the profile as it stands. Anything corrected on the candidate afterwards does not follow the trainee across - correct it on the trainee instead.'),
        __('The medical column is worked out from the check-ups, not typed: a result marked not fit shows as failed and stops both the proposal and the promotion. If it is blank, either no check-up has been recorded or nobody has yet said what that result type means.'),
    ],
];
