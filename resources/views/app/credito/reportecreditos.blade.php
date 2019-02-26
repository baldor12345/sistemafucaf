<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
        .linebordercenter tr th, td{
            text-align : center;
            border: 0.9px solid #3f3f3f;
            
            font-size: 10px;
        }
        table thead {
        color: #000000;
        font-weight:bold;
        background-color: #dee1e5;
        font-size: 12px;
        
        }
        table th {
        color: #000000;
        font-weight:bold;
        background-color: #dee1e5;
        }

        </style>
</head>
    <body>
        <h2 width="70%" style="text-align: center; margin: 20px;">Reporte de Creditos desde {{ date('d/m/Y', strtotime($fechainicio)) }} hasta {{ date('d/m/Y', strtotime($fechafinal)) }}</h2>
        <div>
            <table class="linebordercenter" width ="100%">
                <thead class="linebordercenter">
                    <tr>
                        <th>N°</th>
                        <th>Nombre</th>
                        <th>Monto Credito</th>
                        <th>Fecha</th>
                        <th>Periodo</th>
                        <th>Taza interes</th>
                        <th>Estado</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for($i=0; $i<count($listaCreditos); $i++){
                            $fila = "<tr><td>".($i + 1)."</td><td>".$listaCreditos[$i]->apellidos." ".$listaCreditos[$i]->nombres."</td><td>".$listaCreditos[$i]->valor_credito."</td>";
                            $fila = $fila."<td>".date('d/m/Y', strtotime($listaCreditos[$i]->fechai))."</td><td>".$listaCreditos[$i]->periodo."</td><td>".$listaCreditos[$i]->tasa_interes."</td>";
                            $fila = $fila.($listaCreditos[$i]->estado == '1'? "<td style='color: red;'>Cancelado</td>":"<td style='color: green;'>Vigente</td>")."<td>".$listaCreditos[$i]->descripcion."</td></tr>";
                            echo($fila);
                        }
                    ?>
                </tbody>
                <tfoot>
                  
                </tfoot>
            </table>
        </div>

    </body>
</html>
