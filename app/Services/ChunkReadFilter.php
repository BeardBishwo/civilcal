<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the rows that are between the start and the end rows
        if (($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }
        return false;
    }
}
