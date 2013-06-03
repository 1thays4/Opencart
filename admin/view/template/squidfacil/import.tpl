<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php
        foreach ($breadcrumbs as $breadcrumb) {
        if ($breadcrumb['href']) {
        ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php
        } else {
        ?>
        <?php echo $breadcrumb['separator']; ?><?php echo $breadcrumb['text']; ?>
        <?php
        }
        }
        ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <a onclick="$('#form').submit();" class="button">
                    <?php echo $button_import; ?>
                </a>
            </div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><?php echo $entry_category; ?></td>
                        <td><input type="text" name="category" value="" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <div id="product-category" class="scrollbox">
                                <?php $class = 'odd'; ?>
                                <?php foreach ($product_categories as $product_category) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div id="product-category<?php echo $product_category['category_id']; ?>" class="<?php echo $class; ?>"><?php echo $product_category['name']; ?><img src="view/image/delete.png" alt="" />
                                    <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr> 
                </table>
            </form>
        </div>
    </div>
</div>
<script>
                    $.widget('custom.catcomplete', $.ui.autocomplete, {
                        _renderMenu: function(ul, items) {
                            var self = this, currentCategory = '';

                            $.each(items, function(index, item) {
                                if (item.category != currentCategory) {
                                    ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');

                                    currentCategory = item.category;
                                }

                                self._renderItem(ul, item);
                            });
                        }
                    });
// Category
                    $('input[name=\'category\']').autocomplete({
                        delay: 500,
                        source: function(request, response) {
                            $.ajax({
                                url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request.term),
                                dataType: 'json',
                                success: function(json) {
                                    response($.map(json, function(item) {
                                        return {
                                            label: item.name,
                                            value: item.category_id
                                        }
                                    }));
                                }
                            });
                        },
                        select: function(event, ui) {
                            $('#product-category' + ui.item.value).remove();

                            $('#product-category').append('<div id="product-category' + ui.item.value + '">' + ui.item.label + '<img src="view/image/delete.png" alt="" /><input type="hidden" name="product_category[]" value="' + ui.item.value + '" /></div>');

                            $('#product-category div:odd').attr('class', 'odd');
                            $('#product-category div:even').attr('class', 'even');

                            return false;
                        },
                        focus: function(event, ui) {
                            return false;
                        }
                    });

                    $('#product-category div img').live('click', function() {
                        $(this).parent().remove();

                        $('#product-category div:odd').attr('class', 'odd');
                        $('#product-category div:even').attr('class', 'even');
                    });
</script>
<?php echo $footer; ?> 