<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\DistribucionUtilidades;
use App\Persona;
?>

<style>
	.tablesimple tr th, td {
		text-align : center;
		border: 0.9px solid #b4bdc1;
		font-size: 13px;
		padding: 2px;

	}
	.borderond {
		border-radius: 6px;
	}
	.tablesimple thead {
	color: #000000;
	font-weight:bold;
	background-color: #f2f6f7;
	border: 0.9px solid #b4bdc1;
	}
	.tablesimple th {
	color: #000000;
	font-weight:bold;
	background-color: #f2f6f7;
	border: 0.9px solid #b4bdc1;
	}
	
	
	
</style>
<div class="form-row">
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
                if($indice == 0){
                    $sumatotal_acc_mes += ($numero_acciones_hasta_enero[0]->cantidad_total + $acciones_mensual[$indice]->cantidad_mes) * $j;
                }else{
                    $sumatotal_acc_mes += $acciones_mensual[$indice]->cantidad_mes * $j;
                }
            $j--;
            $indice++;
        }
    }

    $factores_mes=array();
    $f=0;
	$factor = ($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0;
	// echo('factor: '.$factor.'-  UtlD: '.$utilidad_dist);
	// echo('- SUM_T_ACC: '.$sumatotal_acc_mes);
        for ($i=12; $i >0 ; $i--) { 
			// echo('<td align="center">'.round($i * $factor,4)."</td>");
            $factores_mes[$f] = $i * $factor;
            $f++;
        }

    $total_utilidades_faltante = 0;
    ?>


	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr ><th colspan="5" >Distribucion de utilidades faltante</th></tr>
				<tr ><th>N°</th><th>SOCIOS</th><th>TOTAL</th><th>RETIRAR</th><th>AHORRAR</th></tr>
			</thead>
			<tbody>
				<?php
				$socios = Persona::where('tipo','=','SC')->orwhere('tipo','=','S')->get();
				$is=0;
			
				//*************************************************************************** */
				for($i=0; $i< count($socios); $i++){
					
					$listaAcciones = DistribucionUtilidades::list_acciones_por_persona_mes($socios[$i]->id, $anio)->get();
					$num_accionesenero = DistribucionUtilidades::list_enero($socios[$i]->id, ($anio-1))->get();
					$is=0;
					$utilidades = array();
					 if((count($listaAcciones) + count($num_accionesenero))>0){
						// echo("<tr><td rowspan='2'  align='center'>".($i+1)."</td><th rowspan='2' colspan='2' align='left'>".$socios[$i]->nombres." ".$socios[$i]->apellidos."</th>");
						echo('<tr><td>'.($is+1).'</td><td style="text-align: left;">'.$socios[$i]->nombres.' '.$socios[$i]->apellidos.'</td>');
						
						$l=0;
						$sumtotalAcciones =0;
						for($j=1; $j<=12; $j++){
							$numaccciones = 0;
							if($j == 1){
								$numaccciones = count($num_accionesenero) >0?$num_accionesenero[0]->cantidad_total:0;
							}
								
							if(((($l)< (count($listaAcciones)))?$listaAcciones[$l]->mes:"") == $j){
								$numaccciones += (count($listaAcciones)>0)?$listaAcciones[$l]->cantidad_mes:0;
								
							}
							if($numaccciones>0){
								$utilidades[$j-1] = $factores_mes[$j-1] * $numaccciones;
								$sumtotalAcciones += $numaccciones;
								$l++;
								// echo("<td align='center'>".($numaccciones>0?$numaccciones:"-")."</td>");
							}else{
								// echo("<td align='center'>-</td>");
								$utilidades[$j-1] = 0;
							}
							
						}

						// echo("<td align='center'>0</td><td>".round($sumtotalAcciones,1)."</td><td>-</td><td></td><td></td></tr><tr>");
							$sumtotal_util = 0;
						for($j=1; $j<=12; $j++){
							// echo("<td align='center'>".round($utilidades[$j-1],1)."</td>");
							$sumtotal_util += $utilidades[$j-1];
						}
						$total_utilidades_faltante += round(($distribucion->porcentaje_faltante/100)*$sumtotal_util,1);
                         echo('<td>'.round($sumtotal_util - ($distribucion->porcentaje_distribuido/100)*$sumtotal_util,1).'</td>');
                  
						?>
						 <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-xs btnretirarf','vr'=>'1','num'=>''.$is ,'id' => 'btnf'.$is, 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$socios[$i]->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$sumtotal_util,1))) !!}</td>
                       <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-xs btnahorrarf','vr'=>'0', 'num'=>''.$is , 'id' => 'btnfa'.$is , 'onclick' => 'btncli(this)',  'persona_id' => ''.$socios[$i]->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$sumtotal_util,1))) !!}</td>
                      
						<?php
						echo("</tr>");
						$is++;
					 }
				}

                ?>
                <tr>
                    <td>{{ $is+1 }}</td><td style="text-align: left;">{{  $fsocial->nombres.' '.$fsocial->apellidos }}</td><td>{{ round($utilidad_neta*0.1 - ($distribucion->porcentaje_distribuido/100)*$utilidad_neta*0.1, 1) }}</td>
                    <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-xs btnretirarf','vr'=>'1','num'=>''.($is) ,'id' => 'btnf'.($is), 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$fsocial->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$utilidad_neta*0.1, 1))) !!}</td>
                       <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-xs btnahorrarf','vr'=>'0', 'num'=>''.($is) , 'id' => 'btnfa'.($is), 'onclick' => 'btncli(this)',  'persona_id' => ''.$fsocial->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$utilidad_neta*0.1, 1))) !!}</td>
                </tr>
                <tr>
                    <td>{{ $is + 2 }}</td><td style="text-align: left;">{{  $rlegal->nombres.' '.$rlegal->apellidos }}</td><td>{{ round(($distribucion->porcentaje_faltante/100)*$utilidad_neta*0.1, 1) }}</td>
                    <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-xs btnretirarf','vr'=>'1','num'=>''.($is+1) ,'id' => 'btnf'.($is+1), 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$rlegal->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$utilidad_neta*0.1, 1))) !!}</td>
                    <td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-xs btnahorrarf','vr'=>'0', 'num'=>''.($is+1) , 'id' => 'btnfa'.($is+1) , 'onclick' => 'btncli(this)',  'persona_id' => ''.$rlegal->id , 'utilidad'=> ''.round(($distribucion->porcentaje_faltante/100)*$utilidad_neta*0.1, 1))) !!}</td>
                </tr>

			</tbody>
			<tfoot>
				<tr>
					<th>TOTAL</th>
				
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
				
					<th >Utilidades</th>
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
                    <th>{{ round($total_utilidades_faltante, 1) }}</th>
                    <th width="100px">{!! Form::button('<i class="fa fa-check fa-sm"></i> RET TODO', array('class' => 'btn btn-warning btn-xs', 'accion'=>'retirar',  'id' => 'btnahorrartodo', 'onclick' => 'marcartodo(this)')) !!}</th>
                    <th width="100px">{!! Form::button('<i class="fa fa-check fa-sm"></i> AHO TODO', array('class' => 'btn btn-success btn-xs','accion'=>'ahorrar', 'id' => 'btnretirartodo', 'onclick' => 'marcartodo(this)')) !!}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
            {!! Form::button('<i class="fa fa-lg"></i> Distribuir', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnGuardarDist'.$entidad, 'onclick' => 'guardarDistFaltante(this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('1020');
	}); 
    function imprimirpdf(url_pdf) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}

    function btnclieck(btn){
		var num = $(btn).attr('num');
		if( $(btn).attr("vr") == '0'){
			bootbox.confirm("¿Seguro que desea Retirar?", function(result){ 
				if(result){
					$(btn).attr("vr",'1');
					$(btn).removeClass( "btn-light" ).addClass("btn-primary");

					$('#btnfa'+num).attr("vr",'0');
					$('#btnfa'+num).removeClass('btn-primary').addClass('btn-light');
				}
				$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
			});
			
		}
	}
	function btncli(btn){
		var num = $(btn).attr('num');
		if( $(btn).attr("vr") == '0'){
			bootbox.confirm("¿Seguro que desea Ahorrar?", function(result){ 
				if(result){
					$(btn).attr("vr",'1');
					$(btn).removeClass( "btn-light" ).addClass("btn-primary");

					$('#btnf'+num).attr("vr",'0');
					$('#btnf'+num).removeClass('btn-primary').addClass('btn-light');
				}
				$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
			});
		}
	}
	function marcartodo(btn){
		if($(btn).attr('accion')=='retirar'){
			bootbox.confirm("¿Retirar todos?", function(result){ 
				$('.btnretirarf').each(function() {
					var num = $(this).attr('num');
					$(this).attr("vr",'1');
					$(this).removeClass( "btn-light" ).addClass("btn-primary");
					$('#btnfa'+num).attr("vr",'0');
					$('#btnfa'+num).removeClass('btn-primary').addClass('btn-light');
				});
			});
			$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});
		}else{
			bootbox.confirm("¿Ahorrar todos?", function(result){ 
				$('.btnahorrarf').each(function() {
					var num = $(this).attr('num');
					$(this).attr("vr",'1');
					$(this).removeClass( "btn-light" ).addClass("btn-primary");
					$('#btnf'+num).attr("vr",'0');
					$('#btnf'+num).removeClass('btn-primary').addClass('btn-light');
				});
			});
			$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});
		}
	}

    function guardarDistFaltante(btn){
        $(btn).button("loading...");
        var parametros = "";
		var i=0;
		$('.btnahorrarf').each(function() {
			parametros += "&persona_id"+i+"="+$(this).attr('persona_id')+"&monto"+i+"="+$(this).attr('utilidad')+"&ahorrar"+i+"="+$(this).attr('vr');
			i++;
		});
        parametros += "&numerosocios="+i+"&distribucion_id={{ $distribucion->id }}";
        $.ajax({
            url: "caja/guardar_distribucion_faltante",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){

                  cerrarModal();
                  mostrarMensaje ("Transaccion correcta", "OKS");
            }
        }).fail(function(){
            $(btn).removeClass('disabled');
            $(btn).removeAttr('disabled');
            $(btn).html('Guardar');
                mostrarMensaje ("Error de consulta..", "ERROR");
        });
    }

   
</script>
