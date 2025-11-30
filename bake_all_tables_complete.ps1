# Complete Bake Script for All CMS Databases and Tables
# CakePHP 3.9 Multi-Database Project

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "BAKING ALL TABLES FROM ALL DATABASES" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# cms_lpk_candidates
Write-Host "`n[1/12] Baking cms_lpk_candidates tables..." -ForegroundColor Yellow
bin\cake bake all Candidates --connection cms_lpk_candidates --force
bin\cake bake all CandidateCertifications --connection cms_lpk_candidates --force
bin\cake bake all CandidateCourses --connection cms_lpk_candidates --force
bin\cake bake all CandidateDocuments --connection cms_lpk_candidates --force
bin\cake bake all CandidateEducations --connection cms_lpk_candidates --force
bin\cake bake all CandidateExperiences --connection cms_lpk_candidates --force
bin\cake bake all CandidateFamilies --connection cms_lpk_candidates --force
bin\cake bake all CandidateRecordInterviews --connection cms_lpk_candidates --force
bin\cake bake all CandidateRecordMedicalCheckUps --connection cms_lpk_candidates --force
bin\cake bake all MasterCandidateInterviewResults --connection cms_lpk_candidates --force
bin\cake bake all MasterCandidateInterviewTypes --connection cms_lpk_candidates --force

# cms_lpk_candidate_documents
Write-Host "`n[2/12] Baking cms_lpk_candidate_documents tables..." -ForegroundColor Yellow
bin\cake bake all ApprenticeDocumentManagementDashboards --connection cms_lpk_candidate_documents --force
bin\cake bake all CandidateDocumentsMasterList --connection cms_lpk_candidate_documents --force
bin\cake bake all CandidateDocumentCategories --connection cms_lpk_candidate_documents --force
bin\cake bake all CandidateDocumentManagementDashboardDetails --connection cms_lpk_candidate_documents --force
bin\cake bake all CandidateSubmissionDocuments --connection cms_lpk_candidate_documents --force

# cms_masters
Write-Host "`n[3/12] Baking cms_masters tables..." -ForegroundColor Yellow
bin\cake bake all MasterBloodTypes --connection cms_masters --force
bin\cake bake all MasterDepartments --connection cms_masters --force
bin\cake bake all MasterDocumentPreparednessStatuses --connection cms_masters --force
bin\cake bake all MasterDocumentSubmissionStatuses --connection cms_masters --force
bin\cake bake all MasterEmployeeStatuses --connection cms_masters --force
bin\cake bake all MasterEmploymentPositions --connection cms_masters --force
bin\cake bake all MasterFamilyConnections --connection cms_masters --force
bin\cake bake all MasterGenders --connection cms_masters --force
bin\cake bake all MasterIcons --connection cms_masters --force
bin\cake bake all MasterJobCategories --connection cms_masters --force
bin\cake bake all MasterKabupatens --connection cms_masters --force
bin\cake bake all MasterKecamatans --connection cms_masters --force
bin\cake bake all MasterKelurahans --connection cms_masters --force
bin\cake bake all MasterMarriageStatuses --connection cms_masters --force
bin\cake bake all MasterMedicalCheckUpResults --connection cms_masters --force
bin\cake bake all MasterOccupations --connection cms_masters --force
bin\cake bake all MasterOccupationCategories --connection cms_masters --force
bin\cake bake all MasterPropinsis --connection cms_masters --force
bin\cake bake all MasterRejectedReasons --connection cms_masters --force
bin\cake bake all MasterReligions --connection cms_masters --force
bin\cake bake all MasterStratas --connection cms_masters --force
bin\cake bake all MasterTranslations --connection cms_masters --force
bin\cake bake all Sessions --connection cms_masters --force

# cms_tmm_apprentices
Write-Host "`n[4/12] Baking cms_tmm_apprentices tables..." -ForegroundColor Yellow
bin\cake bake all Apprentices --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeCertifications --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeCourses --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeEducations --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeExperiences --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeFamilies --connection cms_tmm_apprentices --force
bin\cake bake all ApprenticeFamilyStories --connection cms_tmm_apprentices --force

# cms_tmm_apprentice_documents
Write-Host "`n[5/12] Baking cms_tmm_apprentice_documents tables..." -ForegroundColor Yellow
bin\cake bake all ApprenticeRecordCoeVisas --connection cms_tmm_apprentice_documents --force
bin\cake bake all ApprenticeRecordMedicalCheckUps --connection cms_tmm_apprentice_documents --force
bin\cake bake all ApprenticeRecordPasports --connection cms_tmm_apprentice_documents --force
bin\cake bake all ApprenticeSubmissionDocuments --connection cms_tmm_apprentice_documents --force
bin\cake bake all MasterApprenticeCoeTypes --connection cms_tmm_apprentice_documents --force
bin\cake bake all MasterApprenticeDepartureDocuments --connection cms_tmm_apprentice_documents --force
bin\cake bake all MasterApprenticeSubmissionDocuments --connection cms_tmm_apprentice_documents --force
bin\cake bake all MasterApprenticeSubmissionDocumentCategories --connection cms_tmm_apprentice_documents --force

# cms_tmm_apprentice_document_ticketings
Write-Host "`n[6/12] Baking cms_tmm_apprentice_document_ticketings tables..." -ForegroundColor Yellow
bin\cake bake all ApprenticeFlights --connection cms_tmm_apprentice_document_ticketings --force

# cms_tmm_organizations
Write-Host "`n[7/12] Baking cms_tmm_organizations tables..." -ForegroundColor Yellow
bin\cake bake all Authorizations --connection cms_tmm_organizations --force
bin\cake bake all MasterAuthorizationRoles --connection cms_tmm_organizations --force
bin\cake bake all Users --connection cms_tmm_organizations --force

# cms_tmm_stakeholders
Write-Host "`n[8/12] Baking cms_tmm_stakeholders tables..." -ForegroundColor Yellow
bin\cake bake all AcceptanceOrganizations --connection cms_tmm_stakeholders --force
bin\cake bake all AcceptanceOrganizationStories --connection cms_tmm_stakeholders --force
bin\cake bake all CooperativeAssociations --connection cms_tmm_stakeholders --force
bin\cake bake all CooperativeAssociationStories --connection cms_tmm_stakeholders --force
bin\cake bake all MasterJapanPrefectures --connection cms_tmm_stakeholders --force
bin\cake bake all SpecialSkillSupportInstitutions --connection cms_tmm_stakeholders --force
bin\cake bake all VocationalTrainingInstitutions --connection cms_tmm_stakeholders --force
bin\cake bake all VocationalTrainingInstitutionStories --connection cms_tmm_stakeholders --force

# cms_tmm_trainees
Write-Host "`n[9/12] Baking cms_tmm_trainees tables..." -ForegroundColor Yellow
bin\cake bake all ApprenticeshipOrders --connection cms_tmm_trainees --force
bin\cake bake all Trainees --connection cms_tmm_trainees --force
bin\cake bake all TraineeCertifications --connection cms_tmm_trainees --force
bin\cake bake all TraineeCourses --connection cms_tmm_trainees --force
bin\cake bake all TraineeEducations --connection cms_tmm_trainees --force
bin\cake bake all TraineeExperiences --connection cms_tmm_trainees --force
bin\cake bake all TraineeFamilies --connection cms_tmm_trainees --force
bin\cake bake all TraineeFamilyStories --connection cms_tmm_trainees --force

# cms_tmm_trainee_accountings
Write-Host "`n[10/12] Baking cms_tmm_trainee_accountings tables..." -ForegroundColor Yellow
bin\cake bake all MasterCurrencies --connection cms_tmm_trainee_accountings --force
bin\cake bake all MasterPaymentMethods --connection cms_tmm_trainee_accountings --force
bin\cake bake all MasterTransactionCategories --connection cms_tmm_trainee_accountings --force
bin\cake bake all TraineeInstallments --connection cms_tmm_trainee_accountings --force

# cms_tmm_trainee_trainings
Write-Host "`n[11/12] Baking cms_tmm_trainee_trainings tables..." -ForegroundColor Yellow
bin\cake bake all TraineeTrainingBatches --connection cms_tmm_trainee_trainings --force

# cms_tmm_trainee_training_scorings
Write-Host "`n[12/12] Baking cms_tmm_trainee_training_scorings tables..." -ForegroundColor Yellow
bin\cake bake all MasterTrainingCompetencies --connection cms_tmm_trainee_training_scorings --force
bin\cake bake all MasterTrainingTestScoreGrades --connection cms_tmm_trainee_training_scorings --force
bin\cake bake all TraineeScoreAverages --connection cms_tmm_trainee_training_scorings --force
bin\cake bake all TraineeTrainingTestScores --connection cms_tmm_trainee_training_scorings --force

# Clear cache after all baking
Write-Host "`n[FINAL] Clearing cache..." -ForegroundColor Green
bin\cake cache clear_all

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "BAKE COMPLETE!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Run fix_all_cross_db_associations.ps1" -ForegroundColor White
Write-Host "2. Verify controllers are not empty (>5KB)" -ForegroundColor White
Write-Host "3. Test in browser" -ForegroundColor White
