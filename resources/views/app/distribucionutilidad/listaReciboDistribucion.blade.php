
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            .linebordercenter tr th{
                text-align : center;
                border: 0.9px solid #3f3f3f;
                font-size: 12px;
            }
            table thead {
                color: #000000;
                font-weight:bold;
                background-color: #dee1e5;
            }
            table th {
                color: #000000;
                font-weight: bold;
                background-color: #dee1e5;
                
            }
            td{
                text-align : center;
                border: 0.9px solid #3f3f3f;
                font-size: 12px;
                padding: 20px 20px;
               
            }

        </style>
</head>
<body>
    <h2 width="70%" style="text-align: center; margin: 20px;">{{ $distribucion->titulo }}</h2>

    </div>
        <table class="linebordercenter" width ="100%">
            <thead class="linebordercenter">
                <tr><th style="width: 5%;"  height="15">N°</th><th height="15" style="width: 45%;">SOCIOS </th><th height="15" style="width: 15%;">Total Acciones</th><th height="15" style="width: 15%;">Utilidad distribuida</th><th height="15" style="width: 20%;">FIRMA</th></tr>
            </thead>
            <tbody>
                <?php
                echo($distrib_util);
                ?>
            </tbody>
            <tfoot class="linebordercenter">
                <tr>
                    <th height="20" colspan="2">TOTAL</th>
                    <th height="20">{{ ($suma_total_acciones > 0?$suma_total_acciones: "-" ) }}</th>
                    <th height="20">{{ round($suma_total_utilidades, 1) }}</th>
                    <th height="20"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>


