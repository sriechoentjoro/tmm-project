<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

class CheckDataShell extends Shell
{
    public function main()
    {
        $this->out('Checking Candidate Data...');
        $connection = ConnectionManager::get('cms_lpk_candidates');
        
        // Fetch candidate with ID 1 (or whatever ID the user is looking at)
        // The user snippet showed "Id 77" for something, but the URL was candidates/view/1.
        // Let's check candidate ID 1 first.
        $candidate = $connection->execute("SELECT id, name, name_katakana, address FROM candidates WHERE id = 1")->fetch('assoc');
        
        if ($candidate) {
            $this->out('Candidate ID: ' . $candidate['id']);
            $this->out('Name: ' . $candidate['name']);
            $this->out('Name Hex: ' . bin2hex($candidate['name']));
            $this->out('Name Katakana: ' . $candidate['name_katakana']);
            $this->out('Name Katakana Hex: ' . bin2hex($candidate['name_katakana']));
            $this->out('Address: ' . $candidate['address']);
            $this->out('Address Hex: ' . bin2hex($candidate['address']));
        } else {
            $this->out('Candidate ID 1 not found.');
        }

        // Also check the "Id 77" entity if it exists in AcceptanceOrganizations or VocationalTrainingInstitutions
        // The snippet showed "Title KOPERASI PERIKANAN CHOSHI", which sounds like an organization.
        
        $connectionMasters = ConnectionManager::get('cms_masters');
        // Try to find "KOPERASI PERIKANAN CHOSHI" in vocational_training_institutions (though I just created it empty, maybe it's in another table?)
        // Or maybe it's in acceptance_organizations?
        
        // Let's check acceptance_organizations in cms_lpk_candidates (or wherever it is)
        // CandidatesTable says belongsTo AcceptanceOrganizations.
        
        // Let's try to find where "KOPERASI PERIKANAN CHOSHI" is.
        // It might be in `acceptance_organizations` table.
        
        $ao = $connection->execute("SELECT * FROM acceptance_organizations WHERE id = 77")->fetch('assoc');
        if ($ao) {
             $this->out('Acceptance Org ID: ' . $ao['id']);
             $this->out('Title: ' . $ao['title']);
             $this->out('Title Hex: ' . bin2hex($ao['title']));
             $this->out('Address: ' . $ao['address']);
             $this->out('Address Hex: ' . bin2hex($ao['address']));
        } else {
             $this->out('Acceptance Org ID 77 not found.');
        }
    }
}
