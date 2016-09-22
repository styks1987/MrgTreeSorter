<?php
	class MrgTreeSorterBehavior extends ModelBehavior{


		public function afterSave(Model $model, $created, $options = []){
			parent::afterSave($model, $created, $options);
			$this->model = $model;
			if($this->model->data[$this->model->alias]['add_to_menu']){
					$menu_item_id = (!empty($this->model->data[$this->model->alias]['menu_item_id']))?$this->model->data[$this->model->alias]['menu_item_id'] : null;
					if($this->_add_to_menu(
							$menu_item_id,
							$this->model->id,
							$this->model->data[$this->model->alias]['link_text'],
							$this->model->data[$this->model->alias]['menu_item_parent_id'],
							$this->model->data[$this->model->alias]['menu_active'],
							$this->model->data[$this->model->alias]['menu_visible_child'])
					){
						// Success
						return true;
					}else{
						// Failed
						return false;
					}
				}else{
					$this->_remove_from_menu($this->model->id);
					return true;
				}
				return false;
		}



		/**
		* add a page to the menu
		*
		* Date Added: Wed, Sep 03, 2014
		*/

		protected function _add_to_menu($menu_item_id = null, $node_id, $link_text, $parent_id, $active=0, $visible_child =0){
			App::import('Model', 'MenuItem');
			$this->MenuItem = new MenuItem();
			$menu_item = [
				'foreign_key'=>$node_id,
				'model'=>$this->model->alias,
				'link_text'=>$link_text,
				'parent_id'=>$parent_id,
				'active'=>$active,
				'visible_child'=>$visible_child,
				'ordering'=>100000
			];
			if(!$menu_item_id){
				$this->MenuItem->create();
			}else{
				$this->MenuItem->id = $menu_item['id'] = $menu_item_id;
				$menu_item['ordering'] = $this->MenuItem->field('ordering');
			}

			if($this->MenuItem->save($menu_item)){
				return true;
			}else{
				return false;
			}
		}

		/**
		 * remove a menu item tied to a piece of content
		 *
		 * Date Added: Wed, Sep 03, 2014
		 */

		protected function _remove_from_menu($id){
			App::import('Model', 'MenuItem');
			$this->MenuItem = new MenuItem();
			$this->MenuItem->deleteAll(['MenuItem.foreign_key'=>$id, 'MenuItem.model'=>$this->model->alias]);
		}
	}
?>
