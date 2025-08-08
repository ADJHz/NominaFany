<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NominasImport;
use App\Models\Nomina;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use ZipArchive;

class NominaController extends Controller
{
    /**
     * Muestra la vista para subir el archivo de nóminas.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        try {
            return Inertia::render('Nominas');
        } catch (\Exception $e) {
            Log::error('Error al renderizar la página de nóminas: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al cargar la página.',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Procesa el archivo de nóminas, guarda los datos y genera los recibos en PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function procesar(Request $request)
    {
        // 1. Validar que el archivo sea un .xlsx y que no esté vacío.
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            // Asegura que la carpeta de recibos exista
            $recibosPath = public_path('recibos');
            if (!File::exists($recibosPath)) {
                File::makeDirectory($recibosPath, 0777, true, true);
                Log::info('Directorio de recibos creado: ' . $recibosPath);
            }

            // Limpia la tabla antes de la nueva importación
            Nomina::truncate();
            Log::info('Tabla nominas truncada.');

            // Verifica que el archivo se haya subido correctamente
            if (!$request->hasFile('excel_file')) {
                Log::error('No se recibió ningún archivo.');
                return redirect()->back()->with('error', 'No se recibió ningún archivo.');
            }

            $collection = Excel::toCollection(new NominasImport, $request->file('excel_file'));
            Log::info('Archivo Excel importado. Total hojas: ' . $collection->count());

            $generatedReceipts = [];

            // Recorre cada hoja y usa el nombre de la hoja como subcarpeta
            foreach ($collection as $sheetName => $sheet) {
                $rows = collect($sheet);
                Log::info("Procesando hoja: $sheetName, total filas: " . $rows->count());

                $subfolderPath = $recibosPath . DIRECTORY_SEPARATOR . $sheetName;
                if (!File::exists($subfolderPath)) {
                    File::makeDirectory($subfolderPath, 0777, true, true);
                    Log::info("Subcarpeta de recibos creada: $subfolderPath");
                }

                // Unificar todos los recibos de la hoja en un solo PDF
                $allHtml = '';
                $validRows = $rows->filter(function($row) {
                    return !empty($row['nombre_empleado'] ?? null);
                });

                $totalValidRows = $validRows->count();
                $currentRow = 0;

                foreach ($rows as $row) {
                    $nombre = $row['nombre_empleado'] ?? null;
                    if (empty($nombre)) {
                        continue;
                    }
                    $currentRow++;

                    $puesto = $row['puesto'] ?? null;
                    $sueldo_diario = isset($row['sueldo_diario']) && is_numeric($row['sueldo_diario']) ? (float)$row['sueldo_diario'] : null;
                    $dias_trabajados = (isset($row['dias_trab']) && is_numeric($row['dias_trab'])) ? (float)$row['dias_trab'] : 0;
                    $sueldo_semanal = isset($row['sueldo_semanal']) && is_numeric($row['sueldo_semanal']) ? (float)$row['sueldo_semanal'] : null;
                    $deposito_bancario = isset($row['deposito_bancario']) && is_numeric($row['deposito_bancario']) ? (float)$row['deposito_bancario'] : null;
                    $sueldo_a_pagar = isset($row['sueldo_a_pagar']) && is_numeric($row['sueldo_a_pagar']) ? (float)$row['sueldo_a_pagar'] : 0;
                    $sueldo_fijo = isset($row['sueldo_fijo']) && is_numeric($row['sueldo_fijo']) ? (float)$row['sueldo_fijo'] : null;
                    $valor_bono = isset($row['valor_bono']) && is_numeric($row['valor_bono']) ? (float)$row['valor_bono'] : null;
                    $bono_obtenido = isset($row['bono_obtenido']) && is_numeric($row['bono_obtenido']) ? (float)$row['bono_obtenido'] : null;

                    $nomina = Nomina::create([
                        'nombre' => $nombre,
                        'puesto' => $puesto,
                        'sueldo_diario' => $sueldo_diario,
                        'dias_trabajados' => $dias_trabajados,
                        'sueldo_semanal' => $sueldo_semanal,
                        'deposito_bancario' => $deposito_bancario,
                        'sueldo_a_pagar' => $sueldo_a_pagar,
                        'sueldo_fijo' => $sueldo_fijo,
                        'valor_bono' => $valor_bono,
                        'bono_obtenido' => $bono_obtenido,
                    ]);

                    // Agregar el recibo actual
                    $allHtml .= view('pdf.recibo', compact('nomina'))->render();

                    // Solo agregar salto de página si no es el último recibo
                 /*   if ($currentRow < $totalValidRows) {
                        $allHtml .= '<div style="page-break-before: always;"></div>';
                    }*/
                }

                // Genera un solo PDF por hoja
                $pdfName = str_replace(' ', '_', $sheetName) . '_Recibos_Nomina_' . now()->format('Ymd') . '.pdf';
                $pdfPath = $subfolderPath . DIRECTORY_SEPARATOR . $pdfName;

                $pdf = Pdf::loadHTML($allHtml)->setPaper('A4');
                $pdf->save($pdfPath);

                Log::info('PDF unificado generado: ' . $pdfPath);

                $generatedReceipts[] = $sheetName . '/' . $pdfName;
            }

            if (empty($generatedReceipts)) {
                Log::error('No se generó ningún recibo. Verifica el contenido del archivo.');
                return redirect()->back()->with('error', 'No se generó ningún recibo. Verifica el contenido del archivo.');
            }

            Log::info('Recibos generados correctamente: ' . implode(', ', $generatedReceipts));

            return redirect()->back()->with([
                'success' => 'Los recibos de nómina se han generado exitosamente.',
                'files' => $generatedReceipts
            ]);
        } catch (\Maatwebsite\Excel\Exceptions\NoFilenameGivenException $e) {
            Log::error('No se ha subido ningún archivo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se ha subido ningún archivo.');
        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Descarga todos los recibos de nómina en un archivo ZIP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function descargarRecibos(Request $request)
    {
        $fileNames = $request->input('files');
        $zipFileName = 'recibos_nomina_' . now()->format('YmdHis') . '.zip';
        $zipFilePath = public_path($zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($fileNames as $fileName) {
                $filePath = public_path('recibos/' . $fileName);
                if (File::exists($filePath)) {
                    $zip->addFile($filePath, $fileName);
                }
            }
            $zip->close();
        }

        // Limpiar los archivos individuales después de crear el ZIP
        foreach ($fileNames as $fileName) {
            File::delete(public_path('recibos/' . $fileName));
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
