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
}
