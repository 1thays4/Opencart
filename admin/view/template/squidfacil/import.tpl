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
                <?php
                    foreach($selected as $option){
                        ?>
                        <input type="hidden" name="selected[]" value="<?php echo $option; ?>" />
                        <?php
                    }
                ?>
                <table class="form">
                    <tr>
                        <td><?php echo $entry_store; ?></td>
                        <td>
                            <div class="scrollbox">
                                <?php $class = 'even'; ?>
                                <div class="<?php echo $class; ?>">
                                    <?php if (in_array(0, $product_store)) { ?>
                                    <input type="checkbox" name="product_store[]" value="0" checked="checked" />
                                    <?php echo $text_default; ?>
                                    <?php } else { ?>
                                    <input type="checkbox" name="product_store[]" value="0" />
                                    <?php echo $text_default; ?>
                                    <?php } ?>
                                </div>
                                <?php foreach ($stores as $store) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?>">
                                    <?php if (in_array($store['store_id'], $product_store)) { ?>
                                    <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                                    <?php echo $store['name']; ?>
                                    <?php } else { ?>
                                    <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                                    <?php echo $store['name']; ?>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_language; ?></td>
                        <td>
                            <input type="hidden" name="product_language_all" value="0"/>
                            <input id="selectall" type="checkbox" name="product_language_all" value="1" checked="checked"/>
                            <?php echo $text_all; ?>
                            <div class="scrollbox">
                                <?php foreach ($languages as $language) { ?>
                                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                                <div class="<?php echo $class; ?>">
                                    <input type="checkbox" class="languages" name="product_language[]" value="<?php echo $language['language_id']; ?>" checked="checked"/>
                                    <?php echo $language['name']; ?>
                                </div>
                                <?php } ?>
                            </div></td>
                    </tr>
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
$('#selectall').on('click', function() {
                    $('.languages').attr('checked', $(this).is(":c hecked"));
                    });
$('.languages').on('change', function(){
    
});
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