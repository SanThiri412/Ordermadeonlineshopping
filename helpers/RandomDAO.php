<?php
    require_once "DAO.php";

    class Random{
        public string $goodsCode;
        public string $goods_image;
    }
    class RandomDAO{    
        public function get_random_image(int $limit = 6){
            $dbh=DAO::get_db_connect();

            $sql="SELECT TOP {$limit} goodsCode,goods_image FROM goods where recommend=1 ORDER BY NEWID();";
            $stmt=$dbh->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS,'Random');
        }
    }
?>