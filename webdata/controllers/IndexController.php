<?php

class IndexController extends Pix_Controller
{
    public function dataAction()
    {
        list(, /*index*/, /*data*/, $id) = explode('/', $this->getURI());
        if (!$dataset = DataSet::find(intval($id))) {
            return $this->redirect('/');
        }

        $this->view->dataset = json_decode($dataset->data);
        $this->view->types = $this->view->dataset->types;
        $this->view->name = $dataset->name;
        $this->view->title = $dataset->name . ' :: ';
    }

    public function listAction()
    {
        if ($_GET['type'] == 'search') {
            $this->view->datasets = DataSet::search("POSITION('" . addslashes($_GET['q']) . "' IN CONCAT(data->>'name', data->>'dataDesc', data->>'columnDesc', data->>'keyword')) > 0");
            $this->view->desc = '搜尋「' . strval($_GET['q']) . '」結果';
            return;
        }

        if ($_GET['dataFormat']) {
            $this->view->datasets = DataSet::search("POSITION('" . addslashes($_GET['dataFormat']) . "' IN data->>'types') > 0");
            $this->view->desc = "資料格式：" . $_GET['dataFormat'];
            $this->view->filter_type = 'dataFormat';
            return;
        }

        $support_query = array(
            'dataTypeName' => '資料集類型',
            'orgFullname' => '資料集提供機關',
            'kindName' => '主題分類',
            'cateCodeName' => '服務分類',
        );
        foreach ($support_query as $type => $type_name) {
            if (array_key_exists($type, $_GET)) {
                $this->view->datasets = DataSet::search("data->>'{$type}' = '" . addslashes($_GET[$type]) . "'");
                $this->view->desc = $type_name . '：' . $_GET[$type];
                $this->view->filter_type = $type;
                return;
            }
        }
        $this->view->datasets = DataSet::search(1);
        $this->view->desc = '全部資料集';
    }

    public function indexAction()
    {
    }
}
