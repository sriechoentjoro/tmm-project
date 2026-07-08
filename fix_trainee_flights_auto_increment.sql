-- Fix: trainee_flights.id was created without AUTO_INCREMENT
-- (its twin apprentice_flights has it), so every INSERT failed with
-- "Field 'id' doesn't have a default value". This blocked adding flight
-- legs from /tickets/transit. Existing ids are preserved; the counter
-- continues from MAX(id)+1.

ALTER TABLE cms_tmm_trainee_document_ticketings.trainee_flights
    MODIFY id INT NOT NULL AUTO_INCREMENT;
