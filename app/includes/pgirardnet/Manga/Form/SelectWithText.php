<?php

namespace pgirardnet\Manga\Form;

class SelectWithText {
    protected $variables = array(
        'label' => '',
        'name' => '',
        'options' => array(),
        'select_box_attributes' => array(),
        'select_box_multiple' => false,
        'select_box_default' => null,
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
    
    public function defaultOptions($defaults) {
        if (count($defaults) > 0 && is_object($defaults[0])) {
            $ids = array();
            foreach ($defaults as $default) {
                $ids[] = $default->id;
            }
            $this->variables['select_box_default'] = $ids;
        }
        elseif (is_object($defaults) && get_class($defaults) != 'Illuminate\Database\Eloquent\Collection') {
            $this->variables['select_box_default'][] = $defaults->id;
        }
        elseif (is_array($defaults)) {
            $this->variables['select_box_default'] = $defaults;
        }
        return $this;
    }
    
    public function make() {
        return \View::make('helpers.selectWithText')->with($this->variables);
    }
}