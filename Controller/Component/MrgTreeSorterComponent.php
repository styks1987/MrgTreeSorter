<?php
	class MrgTreeSorterComponent extends Component{
		public function beforeRender(Controller $controller){
			parent::beforeRender($controller);
			// If the file is being edited we need to set the menu item
			if($controller->params['action'] == 'admin_edit' && !empty($controller->data[$controller->modelClass]['id'])){
				App::import('Model', 'MenuItem');
				$this->MenuItem = new MenuItem();
				$menu_item = $this->MenuItem->find('first', ['conditions'=>['MenuItem.model'=>$controller->modelClass, 'MenuItem.foreign_key'=>$controller->data[$controller->modelClass]['id']]]);
				$controller->set(compact('menu_item'));
			}
		}
	}
?>
