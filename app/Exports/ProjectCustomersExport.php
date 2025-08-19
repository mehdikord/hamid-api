<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectCustomersExport implements fromArray,WithHeadings
{
    protected $data;
    protected $headers;

    public function __construct($data,$headers)
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
