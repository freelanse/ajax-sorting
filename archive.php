<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>тест</title>
    <?php wp_head(); ?>
</head>
<body>
<select id="orderby">
    <option value="date">По дате</option>
    <option value="title">По названию</option>
    <option value="price">По цене</option>
    <option value="width">По ширине</option>
</select>

<select id="order">
    <option value="ASC">По возрастанию</option>
    <option value="DESC">По убыванию</option>
</select>

<div id="product-list"></div>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

<script>
    jQuery(document).ready(function($) {
        function loadProducts(page = 1) {
            const orderby = $('#orderby').val();
            const order = $('#order').val();

            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'filter_products',
                    orderby: orderby,
                    order: order,
                    paged: page,
                },
                success: function(data) {
                    $('#product-list').html(data);
                },
                error: function() {
                    console.error('Ошибка при загрузке продуктов');
                }
            });
        }

        $('#orderby, #order').change(function() {
            loadProducts(1);
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadProducts(page);
        });

        loadProducts();
    });

</script>
<?php wp_footer();?>
</body>
</html>
