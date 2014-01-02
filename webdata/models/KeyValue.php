<?php

class KeyValue extends Pix_Table
{
    public function init()
    {
        $this->_name = 'keyvalue';
        $this->_primary = 'key';

        $this->_columns['key'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['value'] = array('type' => 'text');
    }

    public function set($key, $value)
    {
        try {
            KeyValue::insert(Array(
                'key' => $key,
                'value' => $value,
            ));
        } catch (Pix_Table_DuplicateException $e) {
            KeyValue::find($key)->update(array(
                'value' => $value,
            ));
        }
    }

    public function get($key)
    {
        return KeyValue::find($key)->value;
    }
}
