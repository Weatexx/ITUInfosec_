<?php
require_once 'config.php';

class DataManager
{
    private function getFilePath($filename)
    {
        return DATA_DIR . '/' . $filename . '.json';
    }

    private function readJson($filename)
    {
        $path = $this->getFilePath($filename);
        if (!file_exists($path)) {
            return [];
        }
        $json = file_get_contents($path);
        return json_decode($json, true) ?? [];
    }

    private function writeJson($filename, $data)
    {
        $path = $this->getFilePath($filename);
        // Ensure directory exists with secure permissions
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
            chmod(dirname($path), 0755);
        }
        $result = file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        // Set secure file permissions
        if ($result !== false) {
            chmod($path, 0644);
        }
        return $result;
    }

    // --- Speakers (Konuşmacılar) ---
    public function getSpeakers()
    {
        return $this->readJson('speakers');
    }

    public function getSpeaker($id)
    {
        $speakers = $this->getSpeakers();
        foreach ($speakers as $sp) {
            if ($sp['id'] == $id)
                return $sp;
        }
        return null;
    }

    public function addSpeaker($data)
    {
        $speakers = $this->getSpeakers();
        $newId = 1;
        if (!empty($speakers)) {
            $ids = array_column($speakers, 'id');
            $newId = max($ids) + 1;
        }
        $data['id'] = $newId;
        $speakers[] = $data;
        $this->writeJson('speakers', $speakers);
        return $newId;
    }

    public function updateSpeaker($id, $data)
    {
        $speakers = $this->getSpeakers();
        foreach ($speakers as &$sp) {
            if ($sp['id'] == $id) {
                $sp = array_merge($sp, $data);
                $this->writeJson('speakers', $speakers);
                return true;
            }
        }
        return false;
    }

    public function deleteSpeaker($id)
    {
        $speakers = $this->getSpeakers();
        $newSpeakers = [];
        $found = false;
        foreach ($speakers as $sp) {
            if ($sp['id'] != $id) {
                $newSpeakers[] = $sp;
            } else {
                $found = true;
            }
        }
        if ($found) {
            $this->writeJson('speakers', $newSpeakers);
        }
        return $found;
    }

    // --- Sponsors (Sponsorlar) ---
    public function getSponsors()
    {
        return $this->readJson('sponsors');
    }

    public function getSponsor($id)
    {
        $sponsors = $this->getSponsors();
        foreach ($sponsors as $sp) {
            if ($sp['id'] == $id)
                return $sp;
        }
        return null;
    }

    public function addSponsor($data)
    {
        $sponsors = $this->getSponsors();
        $newId = 1;
        if (!empty($sponsors)) {
            $ids = array_column($sponsors, 'id');
            $newId = max($ids) + 1;
        }
        $data['id'] = $newId;
        $sponsors[] = $data;
        $this->writeJson('sponsors', $sponsors);
        return $newId;
    }

    public function updateSponsor($id, $data)
    {
        $sponsors = $this->getSponsors();
        foreach ($sponsors as &$sp) {
            if ($sp['id'] == $id) {
                $sp = array_merge($sp, $data);
                $this->writeJson('sponsors', $sponsors);
                return true;
            }
        }
        return false;
    }

    public function deleteSponsor($id)
    {
        $sponsors = $this->getSponsors();
        $newSponsors = [];
        $found = false;
        foreach ($sponsors as $sp) {
            if ($sp['id'] != $id) {
                $newSponsors[] = $sp;
            } else {
                $found = true;
            }
        }
        if ($found) {
            $this->writeJson('sponsors', $newSponsors);
        }
        return $found;
    }

    // --- General 1:1 Data Helpers ---
    public function getSingleData($filename)
    {
        $data = $this->readJson($filename);
        return isset($data[0]) ? $data[0] : null;
    }

    public function saveSingleData($filename, $newData)
    {
        // Always save as an array with one element for consistency
        $data = [$newData];
        return $this->writeJson($filename, $data);
    }

    public function getAboutUs()
    {
        return $this->getSingleData('about_us');
    }
    public function saveAboutUs($data)
    {
        return $this->saveSingleData('about_us', $data);
    }

    public function getSponsorsSection()
    {
        return $this->getSingleData('sponsors_section');
    }
    public function saveSponsorsSection($data)
    {
        return $this->saveSingleData('sponsors_section', $data);
    }

    public function getHero()
    {
        return $this->getSingleData('hero_content');
    }
    public function saveHero($data)
    {
        return $this->saveSingleData('hero_content', $data);
    }

    public function getApply()
    {
        return $this->getSingleData('apply');
    }
    public function saveApply($data)
    {
        return $this->saveSingleData('apply', $data);
    }

    public function getSpeakersSection()
    {
        return $this->getSingleData('speakers_section');
    }
    public function saveSpeakersSection($data)
    {
        return $this->saveSingleData('speakers_section', $data);
    }
}

$dataManager = new DataManager();
?>