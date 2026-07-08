<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeCertificates Controller
 *
 * @property \App\Model\Table\TraineeCertificatesTable $TraineeCertificates
 */
class TraineeCertificatesController extends AppController
{
    public function index()
    {
        // cms_tmm_trainee_trainings.trainee_certificates: trainee_id, batch_id, certificate_no, issue_date, notes
        $conn = $this->TraineeCertificates->getConnection();
        $rows = $conn->execute(
            'SELECT
                tc.id, tc.trainee_id, tc.batch_id,
                tc.certificate_no,
                tc.issue_date,
                tc.notes,
                t.name          AS trainee_name,
                t.tmm_code      AS trainee_code,
                b.batch_code    AS batch_code,
                ROUND(AVG(tts.score), 1) AS avg_score,
                COUNT(tts.id)            AS test_count
             FROM trainee_certificates tc
             LEFT JOIN cms_tmm_trainees.trainees t
                    ON t.id = tc.trainee_id
             LEFT JOIN training_batches b
                    ON b.id = tc.batch_id
             LEFT JOIN cms_tmm_trainee_training_scorings.trainee_training_test_scores tts
                    ON tts.trainee_id = tc.trainee_id
             GROUP BY tc.id, tc.trainee_id, tc.batch_id, tc.certificate_no,
                      tc.issue_date, tc.notes, t.name, t.tmm_code, b.batch_code
             ORDER BY tc.id DESC'
        )->fetchAll('assoc');
        $this->set(compact('rows'));
    }

    public function view($id = null)
    {
        $conn = $this->TraineeCertificates->getConnection();
        $row  = $conn->execute(
            'SELECT
                tc.id, tc.trainee_id, tc.batch_id,
                tc.certificate_no, tc.issue_date, tc.notes,
                tc.created, tc.modified,
                t.name       AS trainee_name,
                t.tmm_code   AS trainee_code,
                b.batch_code AS batch_code,
                ROUND(AVG(tts.score), 1)  AS avg_score,
                COUNT(tts.id)             AS test_count,
                MAX(tts.score)            AS max_score,
                MIN(tts.score)            AS min_score,
                SUM(CASE WHEN tts.score >= 60 THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN tts.score < 60  THEN 1 ELSE 0 END) AS failed
             FROM trainee_certificates tc
             LEFT JOIN cms_tmm_trainees.trainees t ON t.id = tc.trainee_id
             LEFT JOIN training_batches b           ON b.id = tc.batch_id
             LEFT JOIN cms_tmm_trainee_training_scorings.trainee_training_test_scores tts
                    ON tts.trainee_id = tc.trainee_id
             WHERE tc.id = ?
             GROUP BY tc.id, tc.trainee_id, tc.batch_id, tc.certificate_no,
                      tc.issue_date, tc.notes, tc.created, tc.modified,
                      t.name, t.tmm_code, b.batch_code',
            [$id]
        )->fetch('assoc');

        if (!$row) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        // Per-competency score breakdown for this trainee
        $scoreRows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_training_scorings')->execute(
            'SELECT tts.id, tts.score, tts.test_date,
                    mc.title AS competency,
                    g.title  AS grade, g.description AS grade_desc
             FROM trainee_training_test_scores tts
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_competencies mc
                    ON mc.id = tts.master_training_competency_id
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_test_score_grades g
                    ON g.id  = tts.master_training_test_score_grade_id
             WHERE tts.trainee_id = ?
             ORDER BY tts.test_date DESC, mc.title',
            [$row['trainee_id']]
        )->fetchAll('assoc');

        $this->set(compact('row', 'scoreRows'));
    }

    public function add()
    {
        $traineeCertificate = $this->TraineeCertificates->newEntity();
        if ($this->request->is('post')) {
            $traineeCertificate = $this->TraineeCertificates->patchEntity($traineeCertificate, $this->request->getData());
            if ($this->TraineeCertificates->save($traineeCertificate)) {
                $this->Flash->success(__('The Trainee Certificate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Trainee Certificate could not be saved. Please, try again.'));
        }
        $this->set('traineeCertificate', $traineeCertificate);
    }

    public function edit($id = null)
    {
        $traineeCertificate = $this->TraineeCertificates->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $traineeCertificate = $this->TraineeCertificates->patchEntity($traineeCertificate, $this->request->getData());
            if ($this->TraineeCertificates->save($traineeCertificate)) {
                $this->Flash->success(__('The Trainee Certificate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Trainee Certificate could not be saved. Please, try again.'));
        }
        $this->set('traineeCertificate', $traineeCertificate);
    }

    public function printCertificate($id = null)
    {
        $conn = $this->TraineeCertificates->getConnection();
        $row  = $conn->execute(
            'SELECT
                tc.id, tc.trainee_id, tc.batch_id,
                tc.certificate_no, tc.issue_date, tc.notes,
                t.name       AS trainee_name,
                t.tmm_code   AS trainee_code,
                b.batch_code AS batch_code,
                ROUND(AVG(tts.score), 1)  AS avg_score,
                COUNT(tts.id)             AS test_count,
                SUM(CASE WHEN tts.score >= 60 THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN tts.score < 60  THEN 1 ELSE 0 END) AS failed
             FROM trainee_certificates tc
             LEFT JOIN cms_tmm_trainees.trainees t ON t.id = tc.trainee_id
             LEFT JOIN training_batches b           ON b.id = tc.batch_id
             LEFT JOIN cms_tmm_trainee_training_scorings.trainee_training_test_scores tts
                    ON tts.trainee_id = tc.trainee_id
             WHERE tc.id = ?
             GROUP BY tc.id, tc.trainee_id, tc.batch_id, tc.certificate_no,
                      tc.issue_date, tc.notes, t.name, t.tmm_code, b.batch_code',
            [$id]
        )->fetch('assoc');

        if (!$row) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        $scoreRows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_training_scorings')->execute(
            'SELECT tts.score, tts.test_date,
                    mc.title AS competency,
                    g.title  AS grade
             FROM trainee_training_test_scores tts
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_competencies mc
                    ON mc.id = tts.master_training_competency_id
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_test_score_grades g
                    ON g.id  = tts.master_training_test_score_grade_id
             WHERE tts.trainee_id = ?
             ORDER BY mc.title',
            [$row['trainee_id']]
        )->fetchAll('assoc');

        $this->viewBuilder()->setLayout('print');
        $this->set(compact('row', 'scoreRows'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeCertificate = $this->TraineeCertificates->get($id);
        if ($this->TraineeCertificates->delete($traineeCertificate)) {
            $this->Flash->success(__('The Trainee Certificate has been deleted.'));
        } else {
            $this->Flash->error(__('The Trainee Certificate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
