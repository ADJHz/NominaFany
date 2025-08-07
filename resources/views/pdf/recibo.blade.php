<!DOCTYPE html>
<html>

<head>
    <title>Recibo de Nómina</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .header,
        .footer {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .info-table,
        .nomina-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td,
        .nomina-table th,
        .nomina-table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .nomina-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>RECIBO DE NÓMINA</h3>
        </div>
        <table class="info-table">
            <tr>
                <td colspan="4">
                    <strong>NOMBRE DE LA EMPRESA:</strong> CENTRO DE ABASTOS FANY, S.A. DE C.V.<br>
                    <strong>DOMICILIO:</strong> CALLE NIÑOS HÉROES 101, COL. BONGONI, ATLACAMULCO, MEXICO <strong>C.P. 50450</strong><br>
                    <strong>RFC:</strong> CAF041118KM7
                </td>
                <td colspan="2" class="text-right">
                    <strong>REGISTRO PATRONAL:</strong> N8514161104
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>NOMBRE:</strong> {{ $nomina->nombre }}</td>
                <td><strong>DÍAS TRABAJADOS:</strong> {{ $nomina->dias_trabajados }}</td>
                <td colspan="2"><strong>PERIODO DE PAGO:</strong> Del {{ now()->subWeek()->format('d M') }} al
                    {{ now()->format('d M') }} de {{ now()->format('Y') }}</td>
            </tr>
            <tr>
                <td colspan="3"><strong>PUESTO DEL TRABAJADOR:</strong> {{ $nomina->puesto }}</td>
                <td><strong>SALARIO DIARIO:</strong> ${{ number_format($nomina->sueldo_diario, 2) }}</td>
                <td colspan="2"><strong>SUELDO SEMANAL:</strong> ${{ number_format($nomina->sueldo_semanal, 2) }}</td>
            </tr>
        </table>

        <table class="nomina-table">
            <thead>
                <tr>
                    <th>INGRESOS</th>
                    <th>DEDUCCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sueldo semanal: ${{ number_format($nomina->sueldo_semanal, 2) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Bono obtenido: ${{ number_format($nomina->bono_obtenido, 2) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Total Ingresos:
                            ${{ number_format($nomina->sueldo_a_pagar, 2) }}</strong></td>
                    <td class="text-right"><strong>Total Deduc: $0.00</strong></td>
                </tr>
            </tbody>
        </table>

        <table class="info-table">
            <tr>
                <td><strong>NETO:</strong> ${{ number_format($nomina->sueldo_a_pagar, 2) }}</td>
                <td class="text-right"><strong>TOTAL PAGADO:</strong> ${{ number_format($nomina->sueldo_a_pagar, 2) }}
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>RECIBÍ DE LA COMPAÑÍA, ARRIBA MENCIONADA, LA CANTIDAD NETA, A QUE ESTE DOCUMENTO SE REFIERE, ESTANDO
                CONFORME CON LAS PERCEPCIONES Y LAS DEDUCCIONES QUE EN EL APARECEN ESPECIFICADAS, NO RESERVÁNDOME
                NINGUNA RECLAMACIÓN POSTERIOR.</p>
            <br>
            <br>
            <hr>
            <p>FIRMA DE CONFORMIDAD</p>
            <p>{{ $nomina->nombre }}</p>
        </div>
    </div>
</body>

</html>
