<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reporte</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="UTF-8">
	<style media="all">
		@page {
			margin: 0;
			padding: 0;
		}

		body {
			font-size: 0.875rem;
			font-family: '<?php echo  $font_family ?>';
			font-weight: normal;
			direction: <?php echo $direction ?>;
			text-align: <?php echo $text_align ?>;
			padding: 0;
			margin: 0;
		}

		.gry-color *,
		.gry-color {
			color: #878f9c;
		}

		table {
			width: 100%;
		}

		table th {
			font-weight: normal;
		}

		table.padding th {
			padding: .25rem .7rem;
		}

		table.padding td {
			padding: .25rem .7rem;
		}

		table.sm-padding td {
			padding: .1rem .7rem;
		}

		.border-bottom td,
		.border-bottom th {
			border-bottom: 1px solid #eceff4;
		}

		.text-left {
			text-align: <?php echo $text_align ?>;
		}

		.text-right {
			text-align: <?php echo $not_text_align ?>;
		}
	</style>
</head>

<body>
	<div>
		<div style="background: #eceff4;padding: 1rem;">
			<table>
				<tr>
					<td>
						<img src="data:image/jpg;base64,{{ get_setting('header_logo') }}" height="30"
							style="display:inline-block;">
					<td style="font-size: 1.5rem; text-align:right" class="strong">REPORTE</td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="font-size: 1rem;" class="strong"> {{$datosEmpresa->nombrecomercial}} </td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{$datosEmpresa->direccion}}</td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small">{{$datosEmpresa->email}}</td>
					<td class="text-right small" style="text-align:right"><span class="gry-color small">Pedido:</span>
						<span class="strong">{{$order->pedidos_codigo}}</span></td>
				</tr>
				<tr>
					<td class="gry-color small">{{$datosEmpresa->telefono1}}</td>
					<td class="text-right small" style="text-align:right"><span class="gry-color small">Fecha:</span>
						<span class=" strong">{{$order->emision}}</span></td>
				</tr>
			</table>

		</div>

		<div style="padding-top: 1rem; padding-left: 1rem; padding-right: 1rem;">
			<table>

				<tr>
					<td class="strong small gry-color"></td>
				</tr>
				<tr>
					<td class="strong">{{$cliente->razonsocial}}</td>
				</tr>
				<tr>
					<td class="gry-color small">Dirección</td>
				</tr>
				<tr>
					<td class="gry-color small">Email: {{$cliente->email}}</td>
				</tr>
				<tr>
					<td class="gry-color small">Teléfono:</td>
				</tr>
			</table>
		</div>

		<div style="padding-top: 2rem; padding-left: 2rem; padding-right: 2rem;">
			<table class="padding text-left small border-bottom">
				<thead>
					<tr class="gry-color" style="background: #eceff4;">
						<th width="17%" class="text-center">Código</th>
						<th width="35%" class="text-center">Nombre del Producto</th>
						<th width="8%" class="text-center">Cantidad</th>
						<th width="20%" class="text-center">Precio Unitario</th>
					</tr>
				</thead>
				<tbody class="strong">

					@foreach ($detalle as $key => $pedidoDetail)
					@if ($pedidoDetail)
					<tr>
						@php
						$producto=App\Models\Producto::select('descripcion','productocodigo')->where('productosid',$pedidoDetail->productosid)->get();
						@endphp
						@foreach ($producto as $key => $name)
						<td>
							{{$name->productocodigo}}
						</td>
						<td>
							{{$name->descripcion}}
						</td>
						@endforeach

						<td style="text-align:center">{{ number_format(round($pedidoDetail->cantidaddigitada,2),2) }}
						</td>
						<td class="currency" style="text-align:right">$
							{{ number_format(round($pedidoDetail->preciovisible,2),2) }}</td>

					</tr>
					@endif
					@endforeach
				</tbody>
			</table>
		</div>
		<?php
            $precioCantidad = 0;
        ?>
		<div style="padding-right: 1rem;">
			<table class="text-right sm-padding small strong float-right border">
				<thead>
					<tr>
						<th width="60%"></th>
						<th width="40%"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
						</td>
						<td>
							<table class="text-right sm-padding small strong"
								style="border: 1px solid #eceff4; padding-top:16px; padding-bottom:16px; padding-left:10px;">
								<tbody>
									<tr>
										<th class="gry-color text-left">Subtotal</th>
										<td style="text-align:right">
											@foreach ($detalle as $key => $detalle)
											<?php
                                                        $precioCantidad =  $precioCantidad + $detalle->precio * $detalle->cantidaddigitada;
                                                ?>
											@endforeach
											$ {{ number_format(round($precioCantidad,2),2) }}

										</td>
									</tr>
									<tr>
										<th class="gry-color text-left">Descuento</th>
										<td style="text-align:right">$
											{{ number_format(round($order->totaldescuento,2),2) }}</td>
									</tr>
									<tr>
										<th class="gry-color text-left">Subtotal Neto</th>
										<td style="text-align:right">$
											{{ number_format(round($order->subtotalneto,2),2) }}</td>
									</tr>
									<tr>
										<th class="gry-color text-left">IVA</th>
										<td style="text-align:right">$ {{ number_format(round($order->total_iva,2),2) }}
										</td>
									</tr>
									<tr>
										<th class="text-left strong"> VALOR TOTAL</th>
										<td class="currency" style="text-align:right">$
											{{ number_format(round($order->total,2),2) }}
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
</body>

</html>
