<?php
$modules_options = array();
$modules_options['skip_admin'] = true;
$modules_options['ui'] = true;

$modules = array();
$mod_obj_str = 'modules';
if (isset($is_elements) and $is_elements == true) {
    $mod_obj_str = 'elements';
    $el_params = array();
    if (isset($params['layout_type'])) {
        $el_params['layout_type'] = $params['layout_type'];
    }
    $modules = mw('layouts')->get($el_params);
    if ($modules == false) {
        scan_for_modules($modules_options);
        $el_params['no_cache'] = true;
        mw('module')->get_layouts($el_params);
        $modules = mw('layouts')->get($el_params);
    }
    // $dynamic_layouts = mw('layouts')->get_all('no_cache=1&get_dynamic_layouts=1');
    $dynamic_layouts = false;


} else {
    $modules = mw('module')->get('ui=1');
    $sortout_el = array();
    $sortout_mod = array();
    if (!empty($modules)) {
        foreach ($modules as $mod) {
            if (isset($mod['as_element']) and intval($mod['as_element']) == 1) {
                $sortout_el[] = $mod;
            } else {
                $sortout_mod[] = $mod;
            }
        }
        $modules = array_merge($sortout_el, $sortout_mod);
    }
}




?>
<script type="text/javascript">

    Modules_List_<?php print $mod_obj_str ?> = {}

</script>

<ul class="modules-list list-<?php print $mod_obj_str ?>">
    <?php $def_icon = MW_MODULES_DIR . 'default.png';
    $def_icon = mw('url')->link_to_file($def_icon);
    ?>
    <?php if (isset($dynamic_layouts) and is_array($dynamic_layouts)): ?>
        <?php foreach ($dynamic_layouts as $dynamic_layout): ?>
            <?php if (isset($dynamic_layout['template_dir']) and isset($dynamic_layout['layout_file'])): ?>
                <li data-module-name="layout"
                    template="<?php print $dynamic_layout['template_dir'] ?>/<?php print $dynamic_layout['layout_file'] ?>"
                    data-filter="<?php print $dynamic_layout['name'] ?>" class="module-item" unselectable="on"> <span
                        class="mw_module_hold">
		<?php if (!isset($dynamic_layout['icon'])): ?>
                            <?php $dynamic_layout['icon'] = $def_icon; ?>
                        <?php endif; ?>
                        <span class="mw_module_image"> <span class="mw_module_image_holder"><img
                                    alt="<?php print $dynamic_layout['name'] ?>"
                                    title="<?php isset($dynamic_layout['description']) ? print addslashes($dynamic_layout['description']) : ''; ?>"
                                    class="module_draggable"
                                    data-module-name-enc="layout_<?php print date("YmdHis") ?>"
                                    src="<?php print $dynamic_layout['icon'] ?>"
                                    /> <s class="mw_module_image_shadow"></s></span></span> <span class="module_name"
                                                                                                  alt="<?php isset($dynamic_layout['description']) ? print addslashes($dynamic_layout['description']) : ''; ?>">
		<?php _e($dynamic_layout['name']); ?>
		</span> </span></li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php foreach ($modules as $module_item): ?>
        <?php if (isset($module_item['module'])): ?>
            <?php


            $module_group2 = explode(DIRECTORY_SEPARATOR, $module_item['module']);
            $module_group2 = $module_group2[0];
            ?>
            <?php $module_item['module'] = str_replace('\\', '/', $module_item['module']);

            $module_item['module'] = rtrim($module_item['module'], '/');
            $module_item['module'] = rtrim($module_item['module'], '\\');
            $temp = array();
            if (isset($module_item['categories']) and is_array($module_item['categories']) and !empty($module_item['categories'])) {
                foreach ($module_item['categories'] as $it) {
                    $temp[] = $it['parent_id'];
                }
                $module_item['categories'] = implode(',', $temp);
            }

            ?>
            <?php $module_item['module_clean'] = str_replace('/', '__', $module_item['module']); ?>
            <?php $module_item['name_clean'] = str_replace('/', '-', $module_item['module']); ?>
            <?php $module_item['name_clean'] = str_replace(' ', '-', $module_item['name_clean']);
            if (isset($module_item['categories']) and is_array($module_item['categories'])) {
                $module_item['categories'] = implode(',', $module_item['categories']);
            }

            ?>
            <?php $module_id = $module_item['name_clean'] . '_' . uniqid(); ?>
            <li id="<?php print $module_id; ?>" data-module-name="<?php print $module_item['module'] ?>"
                data-filter="<?php print $module_item['name'] ?>"
                 ondrop="true"
                data-category="<?php isset($module_item['categories']) ? print addslashes($module_item['categories']) : ''; ?>"
                class="module-item <?php if (isset($module_item['as_element']) and intval($module_item['as_element'] == 1) or (isset($is_elements) and $is_elements == true)) : ?> module-as-element<?php endif; ?>"> <span
                    unselectable="on" class="mw_module_hold"
                    title="<?php print addslashes($module_item["name"]); ?>. <?php print addslashes($module_item["description"]) ?> - Drag and drop in your page">
		<script type="text/javascript">
            Modules_List_<?php print $mod_obj_str ?>['<?php print($module_id); ?>'] = {
                id: '<?php print($module_id); ?>',
                name: '<?php print $module_item["module"] ?>',
                title: '<?php print $module_item["name"] ?>',
                description: '<?php print addslashes($module_item["description"]) ?>'

            }


        </script>
                    <?php if ($module_item['icon']): ?>
                        <span class="mw_module_image"> <span class="mw_module_image_holder"><img
                                    alt="<?php print $module_item['name'] ?>"
                                    title="<?php isset($module_item['description']) ? print addslashes($module_item['description']) : ''; ?>"
                                    class="module_draggable"
                                    data-module-name-enc="<?php print $module_item['module_clean'] ?>|<?php print $module_item['name_clean'] ?>_<?php print date("YmdHis") ?>"
                                    src="<?php print $module_item['icon'] ?>"
                                    /> <s class="mw_module_image_shadow"></s></span></span>
                    <?php endif; ?>
                    <span class="module_name"
                          alt="<?php isset($module_item['description']) ? print addslashes($module_item['description']) : ''; ?>">
		<?php _e($module_item['name']); ?>
		</span> </span></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
