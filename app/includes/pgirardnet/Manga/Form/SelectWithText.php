<?php

namespace pgirardnet\Manga\Form;

class SelectWithText {
    protected $variables = array(
        'label' => '',
        'name' => '',
        'options' => array(),
        'select_box_attributes' => array(),
        'select_box_multiple' => false,
        'new_item_link_attributes' => array()
    );
    
    public static function init($label, $name, $options) {
        $object = new SelectWithText();
        $object->variables['label'] = $label;
        $object->variables['name'] = $name;
        $object->variables['options'] = $options;
        return $object;
    }
    
    public function selectBoxAttributes($attributes) {
        $this->variables['select_box_attributes'] = $attributes;
        return $this;
    }
    
    public function selectBoxMultiple() {
        $this->variables['select_box_attributes']['multiple'] = 'multiple';
        $this->variables['select_box_multiple'] = true;
        return $this;
    }
    
    public function duplicateNewItemIn($name) {
        $this->variables['new_item_link_attributes']['data-duplicate-in'] = $name;
        return $this;
    }
    
    public function make() {
        return \View::make('helpers.selectWithText')->with($this->variables);
    }
}