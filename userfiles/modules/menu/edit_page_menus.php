<?php $menus = get_menu(); ?>
<?php  $rand = uniqid(); ?>

<div class="mw-ui-field-holder">
	<label class="mw-ui-label">
		<?php _e("Add to Navigation"); ?>
	</label>
	<div class="relative">
		<div class="mw-ui-field mw-tag-selector mw-selected-menus mw-ui-field-dropdown" id="mw-selected-menus-<?php print $rand; ?>" style="width: 575px;">
			<input type="text" class="mw-ui-invisible-field" placeholder="<?php _e("Click here to add to navigation"); ?>" style="width: 190px;" />
		</div>
		<?php
$content_id = false;
$try_under_parent = false;
$select_default_menu = false;
$add_to_menu = false;
 
 if(isset($params['content_id'])){
	 $content_id = $params['content_id'];
 }
 
 

 
 
  
if($content_id == false){
	if(isset($params['parent']) and $params['parent'] == 0){
	 $select_default_menu = true;
    }
}


 
if(isset($params['parent'])){
	 $try_under_parent = true;
 } 
 
 
  if(isset($params['add_to_menu'])){
	 $add_to_menu = $params['add_to_menu'];
	 $select_default_menu = false;
	 $try_under_parent = true;
 }
 
 if(is_array($menus )): ?>
		<ul id="mw-menu-selector-list-<?php print $rand; ?>" class="mw-menu-selector-list">
			<?php foreach($menus  as $item): ?>
			<li>
				<label class="mw-ui-check">
					<input id="menuid-<?php print $item['id'] ?>" name="add_content_to_menu[]"  <?php if(is_in_menu($item['id'],$content_id) or ($select_default_menu == true and $item['title'] == 'header_menu' ) or ($add_to_menu == true and $item['title'] == $add_to_menu ) ): ?> checked="checked" <?php endif; ?> value="<?php print $item['id'] ?>" type="checkbox">
					<span></span><span class="mw-menuselector-menu-title"><?php print ucwords(str_replace('_', ' ', $item['title'])) ?></span> </label>
			</li>
			<?php endforeach ; ?>
		</ul>
		<?php if(($try_under_parent ) != false): ?>
		<input type="hidden" name="add_content_to_menu_auto_parent" value="1" />
		<?php endif; ?>
		<?php endif; ?>
	</div>
	<script>

          $(document).ready(function(){
              mw.tools.tag({
                  tagholder:'#mw-selected-menus-<?php print $rand; ?>',
                  items: ".mw-ui-check",
                  itemsWrapper: mwd.getElementById('mw-menu-selector-list-<?php print $rand; ?>'),
                  method:'prepend'
              });
          });

  </script> 
</div>
