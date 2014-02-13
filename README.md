data.g0v.ronny.tw
=================

data.gov.tw 的備份

需求
----
* PostsgreSQL > 9.3 (JSON Type 功能)
* PHP > 5.3 (如需要建 dev-server ，需要 PHP 5.4+)

資料庫初始化
------------
1. 先建起 postgresql server 並且有一個帳號密碼
2. 新增 webdata/config.php ，內容增加:

    <?php

    putenv('PGSQL_DATABASE_URL=pgsql://{user}:{password}@{host}/{db_name}')
3. 執行 php webdata/prompt.php ，在裡面執行:

     > DataSet::createTable();
     > KeyValue::createTable();

4. 下載 http://data.gov.tw/sites/default/files/feeds/opendata0101.xml (2014/1/1 的備份檔) 將檔案放入 webdata/scripts 下面
5. 在 webdata/scripts 下面執行 php updater.php

跑起測試環境(PHP 5.4以上)
-------------------------
* 在最上層執行 php -S 0:7777 index.php ，接下來可以在瀏覽器開 http://localhost:7777/ 預覽
