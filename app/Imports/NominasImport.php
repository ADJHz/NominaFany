<?php

// app/Imports/NominasImport.php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NominasImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    /**
     * @return int
     */
    public function headingRow(): int
    {
        // Cambia a la fila 4, que es donde realmente están los encabezados de los datos
        return 4;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // No omitas ninguna fila aquí, ni hagas ningún filtro.
        // Devuelve todas las filas tal cual para que el controlador pueda detectar las secciones.
        return $rows;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        // Solo procesa la primera hoja, para evitar problemas de índices.
        return [
            0 => $this,
            1 => $this,
            2 => $this,
            3 => $this,
            4 => $this,
            5 => $this,
            6 => $this,
            7 => $this,
            8 => $this,
        ];
    }
}
