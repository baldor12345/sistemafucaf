<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css">
        .linebordercenter tr th{
            text-align : center;
            border: 0.9px solid #3f3f3f;
            
            font-size: 10px;
        }
       
        table{
            padding: 1px 6px;
            border-collapse: collapse;
        }
        td{
            text-align : left;
            border: 0.9px solid #3f3f3f;
            /* padding: 6px 5px; */
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
        
            <table class="linebordercenter" width ="100%">
                <thead class="linebordercenter">
                    <tr>
                        <th width="5%">N°</th>
                        <th width="25%">Nombre</th>
                        <th width="10%">Monto Credito</th>
                        <th width="10%">Fecha</th>
                        <th width="5%">Periodo</th>
                        <th width="10%">Taza interes</th>
                        <th width="10%">Estado</th>
                        <th width="25%">Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for($i=0; $i<count($listaCreditos); $i++){
                            $fila = '<tr><td  width="5%">'.($i + 1).'</td><td width="25%"><p style="padding: 6px 5px;">'.$listaCreditos[$i]->apellidos.' '.$listaCreditos[$i]->nombres.'</p></td><td width="10%">'.$listaCreditos[$i]->valor_credito.'</td>';
                            $fila = $fila.'<td width="10%">'.date('d/m/Y', strtotime($listaCreditos[$i]->fechai)).'</td><td width="5%">'.$listaCreditos[$i]->periodo.'</td><td width="10%">'.$listaCreditos[$i]->tasa_interes.' %</td>';
                            if($listaCreditos[$i]->estado != '1'){
                                $fila = $fila.'<td style="color: green;" width="10%">Vigente</td>';
                            }else{
                                $fila = $fila.'<td style="color: red;" width="10%">Cancelado</td>';
                            }
                            $fila = $fila.'<td width="25%">'.$listaCreditos[$i]->descripcion.'</td></tr>';
                            echo($fila);
                        }
                    ?>
                </tbody>
              
            </table>

    </body>
</html>
