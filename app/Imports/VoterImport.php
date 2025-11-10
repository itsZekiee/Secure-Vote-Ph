<?php
// File: `app/Imports/VoterImport.php`
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VoterImport implements ToCollection, WithHeadingRow
{
    /**
     * Receive all rows from the sheet as a Collection.
     *
     * @param  Collection  $rows
     * @return Collection
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }
}
