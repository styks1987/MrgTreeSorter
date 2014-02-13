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
				if(!empty($item['children'])){
					$this->children = $this->Html->tag('ul', $this->build($item['children'], $this->type));
				}else{
					$this->children = '';
				}

				$this->item = $item;

				$menu .= $this->_create_link();

			}
			return $menu;
		}


		/**
			*  Function Name: _create_link
			*  Description: Create a link for the nav menu
			*  Date Added: Fri, Jul 19, 2013
		*/
		private function _create_link(){

			// Each menu has a different way it displays the data.
			// This function delegates the way each link is made for each menu
			// I try to use link default for most menus

			switch($this->type){
				case 'main-menu':
					return $this->_link_main();
					break;
				case 'admin':
					return $this->_link_editable();
					break;
				default:
					return $this->_link_default();
					break;
			}

		}

		private function _link_default(){
			// If the user linked this item directly to an actual object in the database
			// Make sure the link points to that item
			if(!empty($this->item['MenuItem']['model']) && !empty($this->item['MenuItem']['foreign_key'])){
				$link = '/'.Inflector::pluralize(Inflector::underscore($this->item['MenuItem']['model'])).'/view/'.$this->item['MenuItem']['foreign_key'];
			}else{
				$link = $this->item['MenuItem']['link'];
			}

			$menu_item =
				$this->Html->tag('li',
					//$this->Html->div('nestable-item-box',
						$this->Html->tag('span', $this->Html->link($this->item['MenuItem']['link_text'], $link, ['class'=>'nestable-handle-link']), ['class'=>'nestable-handle']).
						$this->children,
					//),
					['class'=>'nestable-item', 'id'=>'MenuItem_'.$this->item['MenuItem']['id']]
				);

			return $menu_item;
		}

		/**
		 * create a link to the edit page instead of the view page
		 *
		 * Date Added: Tue, Feb 11, 2014
		 */

		private function _link_editable(){
			$menu_item =
				$this->Html->tag('li',
						$this->Html->tag('span',
							$this->Html->link($this->item['MenuItem']['link_text'], '/admin/menu_items/edit/'.$this->item['MenuItem']['id'], ['class'=>'nestable-handle-link']).
							$this->Html->link($this->Html->tag('span', '', ['class'=>'glyphicon glyphicon-remove pull-right']), '/admin/menu_items/delete/'.$this->item['MenuItem']['id'],
													['confirm'=>'Are you sure you want to delete this menu item?', 'escape'=>false]),
						['class'=>'nestable-handle']).
						$this->children,
					['class'=>'nestable-item', 'id'=>'MenuItem_'.$this->item['MenuItem']['id']]
				);

			return $menu_item;
		}


		// The specific implementation of the main nav
		private function _link_main(){
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
					$this->Html->link($this->item['MenuItem']['link_text'], $this->item['MenuItem']['link'], array('escape'=>false)).$span.
					$sub_nav,
					array('class'=>$link_class)
				);

			$menu_item = (!empty($this->item['MenuItem']['new_column']) && $this->item['MenuItem']['new_column'])? '</ul><ul class="new_column">'.$menu_item:$menu_item;

			return $menu_item;

		}

	}
?>
