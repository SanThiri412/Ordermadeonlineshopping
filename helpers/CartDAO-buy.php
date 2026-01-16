<?php
    require_once 'DAO.php';
    class Cart{
        public int $member_id;
        public string $goodsCode;
        public string $goodsName;
        public string $goods_image;
        public int $price;
        public string $size;
        public int $num;
        public int $cartCount;
        public ?string $request=null;
    }
    class CartDAO{
        public function get_cart_by_memberid(int $member_id){
            $dbh=DAO::get_db_connect();
            $sql="select c.member_id,goodsName,cartCount,request,price,goods_image,c.goodsCode from Cart as c inner join Goods as g 
                    on c.goodsCode=g.goodsCode where c.member_id=:member_id";
            $stmt=$dbh->prepare($sql);
            $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
            $stmt->execute();

            $data = [];
            while($row = $stmt->fetchObject('Cart')){
                $data[]=$row;
            }
            return $data;
        }
    }
?>