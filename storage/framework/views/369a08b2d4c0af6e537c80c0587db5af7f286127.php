<?php
if(Auth::check()){
if (get_setting('maneja_sucursales') == "on") {
$wishlist = \App\Models\Wishlist::where('clientes_sucursalesid', session('sucursalid'))->get();
}else{
$wishlist = \App\Models\Wishlist::where('clientesid',Auth::user()->clientesid)->get();
}
}else{
$wishlist=0;
}
?>
<a href="<?php echo e(route('wishlist.index')); ?>" class="d-flex align-items-center text-reset">
    <i class="la la-heart-o la-2x opacity-80"></i>
    <span class="flex-grow-1 ml-1">
        <span class="badge badge-primary badge-inline badge-pill"></span>
        <?php if(Auth::check()): ?>
        <span class="badge badge-primary badge-inline badge-pill"><?php echo e(count($wishlist)); ?></span>
        <?php else: ?>
        <span class="badge badge-primary badge-inline badge-pill">0</span>
        <?php endif; ?>
        <span class="nav-box-text d-none d-xl-block opacity-70">Lista de deseos</span>
    </span>
</a><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/wishlist.blade.php ENDPATH**/ ?>