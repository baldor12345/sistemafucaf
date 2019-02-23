
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

    <?php
        $total_acc_mensual  = 0;
        $ind = 0;
        for($i=1; $i<=12; $i++){
            if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
                $total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
                $ind ++;
            }
        }

        $j=12;
        $indice=0;
        $sumatotal_acc_mes = 0;
        
        for($i=1; $i<=12; $i++){
            if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                $sumatotal_acc_mes += $acciones_mensual[$indice]->cantidad_mes * $j;
                $j--;
                $indice++;                                                      
            }
        }

        $factores_mes=array();
        $f=0;
        $factor = ($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0;
        for ($i=12; $i >0 ; $i--) { 
            $factores_mes[$f] = $i * $factor;
            $f++;
        }
    ?>
    </div>
        <table class="linebordercenter" width ="100%">
            <thead class="linebordercenter">
                <tr><th style="width: 5%;"  height="15">N°</th><th height="15" style="width: 45%;">SOCIOS</th><th height="15" style="width: 15%;">Total Acciones</th><th height="15" style="width: 15%;">Utilidad distribuida</th><th height="15" style="width: 20%;">FIRMA</th></tr>
            </thead>
            <tbody>
                <?php
                echo($distrib_util);
                ?>
            </tbody>
            <tfoot class="linebordercenter">
                <tr>
                    <th height="20" colspan="2">TOTAL</th>
                    <?php
                        $total_acc_mensual  = 0;
                        $ind = 0;
                        
                        for($i=1; $i<=12; $i++){
                            if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
                                $total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
                                $ind ++;
                            }
                        }
                    ?>
                    <th height="20">{{ ($total_acc_mensual > 0?$total_acc_mensual: "-" ) }}</th>
                        <?php
                            $j=12;
                            $indice=0;
                            $sumatotal_utilidades = 0;
                            
                            for($i=1; $i<=12; $i++){
                                if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                                    $sumatotal_utilidades += $acciones_mensual[$indice]->cantidad_mes * $factor*$j;
                                    $j--;
                                    $indice++;
                                }
                            }
                        ?>
                   <th height="20">{{ round($sumatotal_utilidades, 1) }}</th>
                    <th height="20"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
