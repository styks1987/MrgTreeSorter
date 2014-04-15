<?php
	App::uses('Helper', 'View');

	/* This class builds the various menus on the site */
	class MrgMenuHelper extends AppHelper{

		var $helpers = array('Html');
		private $item; // A MenuItem entry
		private $type; // What menu are we building
		private $children; // Current Menu Item's Children

		/**
			*  Function Name: build_menu
			*  Description: Used for building the navigational menus
			*  Date Added: Fri, Jul 19, 2013
		*/
		function build($menu_items, $type=null){
			$this->type = $type;
			$menu = '';
			foreach ($menu_items as $item){
				$item['active_class'] = 'inactive';
				// If the top menu is active url or any of the children
				$item['current_page_status'] = $this->_is_active($item);
				$item['active_class'] = $this->_get_active_class($item['current_page_status']);
				if(!empty($item['children'])){
					$this->children = $this->build($item['children'], $this->type);
					$this->children = $this->Html->tag('ul', $this->children, ['class'=>$item['active_class']]);
				}else{
					$this->children = '';
				}
				$menu .= $this->_create_link($item);
			}
			return $menu;
		}


		/**
			*  Function Name: _create_link
			*  Description: Create a link for the nav menu
			*  Date Added: Fri, Jul 19, 2013
		*/
		private function _create_link($item){

			// Each menu has a different way it displays the data.
			// This function delegates the way each link is made for each menu
			// I try to use link default for most menus

			switch($this->type){
				case 'main-menu':
					return $this->_link_main($item);
					break;
				case 'admin':
					return $this->_link_editable($item);
					break;
				default:
					return $this->_link_default($item);
					break;
			}

		}

		private function _link_default($item){
			$link = $this->_get_url($item);

			$menu_item =
				$this->Html->tag('li',
					//$this->Html->div('nestable-item-box',
						$this->Html->tag('span', $this->Html->link($item['MenuItem']['link_text'], $link, ['class'=>'nestable-handle-link']), ['class'=>'nestable-handle']).
						$this->children,
					//),
					['class'=>'nestable-item '.$item['active_class'], 'id'=>'MenuItem_'.$item['MenuItem']['id']]
				);

			return $menu_item;
		}

		/**
		 * create a link to the edit page instead of the view page
		 *
		 * Date Added: Tue, Feb 11, 2014
		 */

		private function _link_editable($item){
			$menu_item =
				$this->Html->tag('li',
						$this->Html->tag('span',
							$this->Html->link($item['MenuItem']['link_text'], '/admin/menu_items/edit/'.$item['MenuItem']['id'], ['class'=>'nestable-handle-link']).
							$this->Html->link($this->Html->tag('span', '', ['class'=>'glyphicon glyphicon-remove pull-right']), '/admin/menu_items/delete/'.$item['MenuItem']['id'],
													['confirm'=>'Are you sure you want to delete this menu item?', 'escape'=>false]),
						['class'=>'nestable-handle']).
						$this->children,
					['class'=>'nestable-item', 'id'=>'MenuItem_'.$item['MenuItem']['id']]
				);

			return $menu_item;
		}


		// The specific implementation of the main nav
		private function _link_main($item){
			$sub_nav = $description = $link_class = $extra = '';


			if(!empty($this->children)){
				// Since this item has children, we will add span to hold the down arrow
				$span = $this->Html->tag('span', '', array('class'=>'arrow'));
				$sub_nav = $this->Html->div('sub_nav',
								$this->Html->div('nav_inner',
									$description.
									$this->children.
									$this->Html->div('clear','').
									$extra
								)
							);
			}else{
				$span = '';
				$sub_nav = '';
			}

			$menu_item =
				$this->Html->tag('li',
					$this->Html->link($item['MenuItem']['link_text'], $item['MenuItem']['link'], array('escape'=>false)).$span.
					$sub_nav,
					array('class'=>$link_class)
				);

			$menu_item = (!empty($item['MenuItem']['new_column']) && $item['MenuItem']['new_column'])? '</ul><ul class="new_column">'.$menu_item:$menu_item;

			return $menu_item;

		}

		/**
		 * get url for the menu item
		 *
		 * Date Added: Mon, Feb 17, 2014
		 */
		private function _get_url($item){

			// If the user linked this item directly to an actual object in the database
			// Make sure the link points to that item
			if(!empty($item['MenuItem']['model']) && !empty($item['MenuItem']['foreign_key'])){
				$link = '/'.Inflector::pluralize(Inflector::underscore($item['MenuItem']['model'])).'/view/'.$item['MenuItem']['foreign_key'];
			}else{
				$link = $item['MenuItem']['link'];
			}
			return $link;
		}


		/**
		 * Is this particular link item active. Does it match
		 * 1 - The current link item is active
		 * 2 - a child link is active
		 * 0 - menu item not active
		 *
		 * Date Added: Mon, Feb 17, 2014
		 */

		private function _is_active($item){
			if($this->_get_url($item) == $this->here){
				// The current page we are looking at is active, so no need to go further
				return 1;
			}else{
				if(!empty($item['children'])){
					// Check the children for an active item
					foreach($item['children'] as $item){
						if($this->_get_url($item) == $this->here){
							return 2;
						}
					}
					return 0;
				}else{
					return 0;
				}

			}
		}

		/**
		 * get the class necessary for the current link item
		 * 1 - The current link item is active
		 * 2 - a child link is active
		 * 0 - menu item not active
		 *
		 * Date Added: Mon, Feb 17, 2014
		 */
		private function _get_active_class($link_status = 0){
			switch ($link_status){
				case 1:
					$class = 'active';
					break;
				case 2:
					$class = 'child-active';
					break;
				case 0:
					$class = 'inactive';
					break;
				default:
					$class = 'inactive';
					break;
			}
			return $class;
		}
	}
?>
