<?php
echo
	$this->Html->tag('h3', 'Menu Settings').
	$this->Form->input('add_to_menu', ['id'=>'add_to_menu', 'checked'=>(!empty($menu_item))?'checked':false,'type'=>'checkbox', 'class'=>'checkbox-inline', 'style'=>'margin:-2px 20px 0 0;', 'label'=>'Do you want this page in the menu?']).
	$this->Html->div('menu_settings',
		$this->Html->tag('p', 'Pages are not automatically added to the main menu. <strong>If you add a menu item at the top level, you could break the layout. Please be sure you understand the implications of adding this to the menu.</strong>').
		$this->Form->hidden('menu_item_id', ['value'=>(!empty($menu_item))?$menu_item['MenuItem']['id']:false]).
		$this->Form->input('link_text', ['value'=>(!empty($menu_item))?$menu_item['MenuItem']['link_text']:false, 'required', 'label'=>'Link Text<br /><small>This will be the text that appears in the menu</small>']).
		$this->Autocomplete->input('menu_item_parent_id', (!empty($menu_item))?$menu_item['MenuItem']['parent_id']:false, (!empty($menu_item))?$menu_item['ParentMenuItem']['link_text']:false, '/admin/menu_items/autocomplete', 'link_text', ['required', 'label'=>'Parent Menu Item<br /><small>Type the title of the parent menu item and choose it from the list.</small>']).
		$this->Form->input('menu_active', ['checked'=>(!empty($menu_item['MenuItem']['active']))?'checked':false, 'class'=>'checkbox-inline' , 'style'=>'margin: 0 20px 0 0;', 'label'=>'Active <small>If active, this menu item will appear on the site even if the page is not published.</small>']).
		$this->Form->input('menu_visible_child', ['checked'=>(!empty($menu_item['MenuItem']['visible_child']))?'checked':false, 'class'=>'checkbox-inline', 'label'=>'Show Children <small>If checked, children of this menu item will appear in the menu</small>', 'type'=>'checkbox', 'style'=>'margin: 0 20px 0 0;']),
		['style'=>(!empty($menu_item))?'display:block':'display:none']
	);

$this->Js->buffer("
	$('.menu_settings input').attr('disabled', !$('#add_to_menu').is(':checked'));
	$('#add_to_menu').change(function () {
		$('.menu_settings').toggle();
		$('.menu_settings input').attr('disabled', !this.checked);
	})
");
?>
