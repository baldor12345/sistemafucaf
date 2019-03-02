<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css">
        .linebordercenter tr{
            text-align : center;
            border: 0.9px solid #3f3f3f;
            font-size: 10px;
        }
       
        table{
            padding: 0px 4px;
            border-collapse: collapse;
        }
        td{
            text-align : left;
            border: 0.9px solid #3f3f3f;
          
        }
        table thead {
            color: #000000;
            font-weight:bold;
            background-color: #cbd3d6;
            font-size: 10px;
            border-t
        }
        th {
            color: #000000;
            font-weight:bold;
            border: 0.9px solid #3f3f3f;
            background-color: #cbd3d6;
            text-align : center;
        }
        .hdr h2{
            background-color: #cbd3d6;
            padding: 0px;
        }
        

        </style>
        
</head>
    <body>
            <h2 width="70%" style="text-align: center; margin: 20px;">{{ $titulo }}</h2>
        <div>
            <?php
                $tbody1 ="";
                $capital_total = 0;
                $contador = 1;
                for ($indice= 0; $indice<count($listaAhorrosActivos) ; $indice++) {
                    if(round($listaAhorrosActivos[$indice]->capital,1) > 0){
                        $fila1 = "<tr>";
                        $fila1 = $fila1.'<td width="4%">'.$contador.'</td>';
                        $fila1 = $fila1.'<td width="50%">'.$listaAhorrosActivos[$indice]->apellidos.' '.$listaAhorrosActivos[$indice]->nombres.'</td>';
                        if(trim($listaAhorrosActivos[$indice]->tipo) == 'S'){
                            $fila1 = $fila1.'<td width="23%">Socio</td>';
                        }else if(trim($listaAhorrosActivos[$indice]->tipo) == 'C'){
                            $fila1 = $fila1.'<td width="23%">Cliente</td>';
                        }else{
                            $fila1 = $fila1.'<td width="23%">Entidad</td>';
                        }
                        $fila1 = $fila1.'<td width="23%"> s/. '.round($listaAhorrosActivos[$indice]->capital,1).'</td></tr>';
                        $tbody1 = $tbody1.$fila1;
                        $contador++;
                    }
                    $capital_total += round($listaAhorrosActivos[$indice]->capital,1);
                }
            ?>

            <table class="linebordercenter" width ="100%">
                <thead class="">
                    <tr>
                        <th width="4%">N°</th>
                        <th width="50%">Socio/Cliente/Entidad</th>
                        <th width="23%">Tipo</th>
                        <th width="23%">Monto de Ahorro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    echo($tbody1);
                    ?>
                </tbody>
                <tfoot>
                    <tr><th colspan="3" width="77%">TOTAL</th><th width="23%">s/. {{ $capital_total }}</th></tr>
                    
                </tfoot>
            </table>
        </div>
      
    </body>
   
</html>
