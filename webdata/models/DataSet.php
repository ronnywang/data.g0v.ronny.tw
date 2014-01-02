<?php

class DataSetRow extends Pix_Table_Row
{
}

class DataSet extends Pix_Table
{
    public function init()
    {
        $this->_primary = 'id';
        $this->_name = 'data_set';
        $this->_rowClass = 'DataSetRow';

        $this->_columns['id'] = array('type' => 'int');
        $this->_columns['name'] = array('type' => 'varchar', 'size' => 64);
        $this->_columns['data'] = array('type' => 'json');
        $this->_columns['search'] = array('type' => 'text');
    }
}
