<div class="aiz-category-menu bg-white rounded <?php if(Route::currentRouteName() == 'home'): ?> shadow-sm" <?php else: ?> shadow-lg"
    id="category-sidebar" <?php endif; ?>>
    <div class="p-3 bg-soft-primary d-none d-lg-block rounded-top all-category position-relative text-left">
        <span class="fw-600 fs-16 mr-3"><?php echo e(ucfirst(get_setting('grupo_productos'))); ?></span>
        <a href="<?php if(get_setting('vista_categorias')==1): ?> <?php echo e(route('categories.all')); ?> <?php else: ?> <?php echo e(route('search')); ?> <?php endif; ?>"
            class="text-reset">
            <span class="d-none d-lg-inline-block">Ver todas</span>
        </a>
    </div>
    <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">
        <?php $__currentLoopData = grupoProductos(11); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="category-nav-element" data-id="1">
            <a href="<?php echo e(route('products.category', $category->id)); ?>"
                class="text-truncate text-reset py-2 px-3 d-block">
                <img class="cat-image lazyload mr-2 opacity-60"
                    src="data:image/jpg;base64,<?php if($category->imagen): ?> <?php echo e(base64_encode($category->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>"
                    width="16" alt="<?php echo e($category->descripcion); ?>">
                <span class="cat-name"><?php echo e($category->descripcion); ?></span>
            </a>


        </li>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


    </ul>

</div><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/category_menu.blade.php ENDPATH**/ ?>