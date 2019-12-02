<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Rank;
use Modules\Installer\Services\Importer\BaseImporter;

class RankImport extends BaseImporter
{
    protected $table = 'ranks';

    public function run($start = 0)
    {
        $this->comment('--- RANK IMPORT ---');

        $count = 0;
        foreach ($this->db->readRows($this->table, $start) as $row) {
            $rank = Rank::firstOrCreate(['name' => $row->rank], [
                    'image_url' => $row->rankimage,
                    'hours'     => $row->minhours,
                ]);

            $this->idMapper->addMapping('ranks', $row->rankid, $rank->id);
            $this->idMapper->addMapping('ranks', $row->rank, $rank->id);

            if ($rank->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' ranks');
    }
}
