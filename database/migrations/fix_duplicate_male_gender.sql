-- Remove the second "Male" from the gender list.
--
-- master_genders holds three rows:
--
--   1  Male    男性
--   2  Female  女性
--   3  Male    picture
--
-- The third is a stray: somebody typed "picture" into the Japanese title, and
-- its created date is 0000-00-00. Every dropdown that reads the table offers
-- Male twice, which is what the trial user reported from the candidate form.
--
-- The rows are moved before the row is removed. Deleting first would leave
-- any record pointing at id 3 referring to a row that is not there, and the
-- gender would read as blank on the CV and in every report that joins it.
-- Three tables carry master_gender_id; there are no others.
--
-- Run bin/query.php first to see how many records this moves:
--
--   php bin/query.php cms_lpk_candidates \
--     "SELECT COUNT(*) FROM candidates WHERE master_gender_id = 3"
--
-- Applied through bin/apply-migration.php, which prints the statements and
-- needs --go to run them.

USE cms_masters;

UPDATE cms_lpk_candidates.candidates
   SET master_gender_id = 1
 WHERE master_gender_id = 3;

UPDATE cms_tmm_trainees.trainees
   SET master_gender_id = 1
 WHERE master_gender_id = 3;

UPDATE cms_tmm_apprentices.apprentices
   SET master_gender_id = 1
 WHERE master_gender_id = 3;

-- Guarded by what the row contains, not only by its id, so that an
-- installation whose ids run differently cannot lose a real gender here.
DELETE FROM master_genders
 WHERE id = 3
   AND title = 'Male'
   AND title_jpg = 'picture';

-- What is left.
SELECT id, title, title_jpg FROM master_genders ORDER BY id;
