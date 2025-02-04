@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="row flex-grow-1">
            <div class="col">
                <h5 class="mb-0 h6">Reseñas de Productos</h5>
            </div>
            <div class="col-md-6 col-xl-4 ml-auto mr-0">
                <form class="" id="sort_by_rating" action="{{ route('reviews') }}" method="GET">
                    <div class="" style="min-width: 200px;">
                        <select class="form-control aiz-selectpicker" name="rating" id="rating"
                            onchange="filter_by_rating()">
                            <option value="">Filtrar por Valoracion</option>
                            <option value="desc">Valoracion (Mayor > Menor)</option>
                            <option value="asc">Valoracion (Menor > Mayor)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Producto</th>
                    @if (get_setting('maneja_sucursales') == "on")
                    <th>Sucursal</th>
                    @else
                    <th data-breakpoints="lg">Cliente</th>
                    @endif
                    <th>Valoracion</th>
                    <th data-breakpoints="lg">Comentario</th>
                    <th data-breakpoints="lg">Publicado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $key => $review)
                <tr>
                    <td>
                        <a href="{{route('product',$review->productosid)}}" target="_blank">{{
                            $review->descripcion}}</a>
                    </td>
                    <td>{{ $review->razonsocial }}</td>
                    <td>{{ $review->valoracion }}</td>
                    <td>{{ $review->comentario }}</td>
                    <td><label class="aiz-switch aiz-switch-success mb-0">
                            <input onchange="update_published(this)" value="{{ $review->ecommerce_comentariosid }}"
                                type="checkbox" <?php if($review->estado == 1) echo "checked";?>>
                            <span class="slider round"></span></label>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $reviews->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    function update_published(el){
            if(el.checked){
                var estado = 1;
            }
            else{
                var estado = 0;
            }
            $.post('{{ route('reviews.publicado') }}', {_token:'{{ csrf_token() }}', ecommerce_comentariosid:el.value, estado:estado}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', 'Reseñas actualizadas correctamente ');
                }
                else{
                    AIZ.plugins.notify('danger', 'Algo salio mal');
                }
            });
        }
    function filter_by_rating(el){
            var rating = $('#rating').val();
            if (rating != '') {
                $('#sort_by_rating').submit();
            }
        }
</script>
@endsection