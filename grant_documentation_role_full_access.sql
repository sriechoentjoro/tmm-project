-- Grant tmm-documentation role (id 5) full CRUD on its document-management menus.
--
-- Background: role_menus.granted_actions = '*' does NOT grant every action.
-- AppController::getMenuRolePermissions() expands '*' to only [menu action, index, view].
-- Controllers that need full CRUD require an explicit comma-separated action list
-- (see the comment in AppController). The documentation role is responsible for all
-- pre-departure documentation management, so its core menus get explicit full lists.
--
-- Menus updated (all children of "TMM Documentation" / "Departure Docs" groups,
-- i.e. non-container menus that actually carry page permissions):
--   81 Trainee Document Submission (TraineeSubmissionDocuments)
--   83 Passport Records            (TraineeRecordPasports)
--   84 COE & Visa Records          (TraineeRecordCoeVisas)
--   70 Health Certificates         (TraineeRecordMedicalCheckUps)
--   85 Tickets & Flights           (Tickets)
--   80 Candidate Documents         (CandidateDocuments)
--   82 Departure Documents         (TraineeDocuments)

USE cms_authentication_authorization;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete,checklist,progress,costs,removeSubmission'
WHERE role_id = 5 AND menu_id = 81;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete'
WHERE role_id = 5 AND menu_id = 83;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete'
WHERE role_id = 5 AND menu_id = 84;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete'
WHERE role_id = 5 AND menu_id = 70;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete,departures,transit,costs'
WHERE role_id = 5 AND menu_id = 85;

UPDATE role_menus SET granted_actions = 'index,view,add,edit,delete,submissionChecklist'
WHERE role_id = 5 AND menu_id = 80;

UPDATE role_menus SET granted_actions = 'index,view,departure,ticketing'
WHERE role_id = 5 AND menu_id = 82;

-- Verify
SELECT rm.menu_id, m.title, m.controller, rm.granted_actions
FROM role_menus rm
JOIN cms_masters.menus m ON m.id = rm.menu_id
WHERE rm.role_id = 5 AND rm.menu_id IN (70, 80, 81, 82, 83, 84, 85);
