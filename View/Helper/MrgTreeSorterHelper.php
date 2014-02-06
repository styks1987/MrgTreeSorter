<?php

	App::uses('Helper', 'View');

	class MrgTreeSorterHelper extends AppHelper{
		public $helpers = ['Js', 'Html', 'MrgTreeSorter.MrgMenu'];

		public $options = [];

		public function __construct(View $view, $settings = array()) {
			parent::__construct($view, $settings);
			$this->options = $settings;
		}


		public function sortable($list, $options = []){
			$this->options = array_merge($this->options, $options);
			$this->Html->script('MrgTreeSorter.jquery.nested_sortable.js', ['inline'=>false]);

			$this->Js->buffer($this->Js->get('#primaryNav')->nestedSortable($this->options));
			$this->Js->buffer('$( "#primaryNav" ).disableSelection();');
			return $this->Html->tag('ul', $this->MrgMenu->build($list), [ 'id'=>'primaryNav']);
		}
	}

?>
