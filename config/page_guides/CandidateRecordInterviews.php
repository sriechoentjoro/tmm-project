<?php
/**
 * Guide for the candidate interview records.
 */

return [
    'icon' => 'fa-comments',
    'title' => __('Candidate Interviews'),
    'subtitle' => __('What the accepting company thought, in five parts and one recommendation.'),
    'lead' => __('An interview is scored on five things - politeness, communication, motivation, adaptability and technical ability - and ends with a recommendation. The five make an overall score out of a hundred, and both the score and the recommendation are written onto the candidate so the promotion screen can show them without anyone retyping.'),

    'actors' => [
        ['role' => 'tmm-recruitment', 'can' => __('Records the interview on behalf of the accepting company.')],
        ['role' => 'administrator', 'can' => __('The same.')],
        ['role' => 'lpk-penyangga', 'can' => __('Sees the result for its own candidates.')],
    ],

    'before' => [
        ['note' => __('The candidate must exist and, in practice, should have been physically tested first - the interview is the second of the three results.'), 'url' => '/candidates', 'label' => __('Candidates')],
    ],

    'steps' => [
        [
            'title' => __('Score the five parts'),
            'who' => __('Recruitment staff'),
            'do' => __('Give each of politeness, communication, motivation, adaptability and technical ability its score, and write what was actually said in the comments. The comments are the part a later reader learns from; the numbers only rank.'),
            'result' => __('An overall score out of 100 is computed and the interview record is saved.'),
            'data' => 'candidate_record_interviews',
        ],
        [
            'title' => __('Give the recommendation'),
            'who' => __('Recruitment staff'),
            'do' => __('Pass, reserved or fail. Reserved is the honest middle when the answer is not yet no.'),
            'result' => __('The recommendation is stored as an interview result and mirrored onto the candidate.'),
            'data' => 'candidates.interview_recommendation',
        ],
        [
            'title' => __('Let it reach the candidate'),
            'who' => __('The system'),
            'do' => __('The five sub-scores, the overall score, the notes and the recommendation are copied onto the candidate record.'),
            'result' => __('The interview is readable from the candidate screens and from the promotion list.'),
            'data' => 'candidates.interview_score',
        ],
    ],

    'diagram' => "graph TD\n"
        . "    A[" . __('Politeness') . "] --> F[" . __('Overall /100') . "]\n"
        . "    B[" . __('Communication') . "] --> F\n"
        . "    C[" . __('Motivation') . "] --> F\n"
        . "    D[" . __('Adaptability') . "] --> F\n"
        . "    E[" . __('Technical') . "] --> F\n"
        . "    F --> G[" . __('Recommendation: pass / reserved / fail') . "]\n"
        . "    G --> H[" . __('Mirrored onto the candidate') . "]\n"
        . "    style F fill:#e3f2fd\n"
        . "    style H fill:#c8e6c9",

    'triggers' => [
        [
            'icon' => 'fa-user-plus',
            'what' => __('The candidate record carries the interview result, so nothing needs to be entered twice.'),
            'url' => '/candidates',
            'label' => __('Candidates'),
        ],
        [
            'icon' => 'fa-chart-line',
            'what' => __('Promotion shows this score beside the physical one, and records a remark if it is missing.'),
            'url' => '/candidates/promote-to-trainee',
            'label' => __('Promote to Trainee'),
        ],
    ],

    'cautions' => [
        __('A second interview for the same candidate overwrites the figures mirrored onto them, although both interview records are kept. The candidate screen therefore shows the latest, not the best.'),
        __('A recommendation of reserved is not a rejection, and nothing in the system turns it into one. Someone has to decide.'),
    ],
];
