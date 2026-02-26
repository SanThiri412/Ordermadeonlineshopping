<?php
    require_once 'DAO.php';
    class Buy{
        public int $member_id;
        public string $goodsCode;
        public int $price;
        public int $buyCount;
        public ?string $request=null;
        public DateTime $buydatetime;
    }
    class BuyDAO{
        public function insert(int $member_id,string $goodsCode,int $price,int $cartCount,?string $request){
            $dbh = DAO::get_db_connect();
            //↓buyテーブルに挿入
            $buy="insert into buy (member_id,buydatetime) output inserted.buy_id Values(:member_id,:buydatetime)";
            $stmt=$dbh->prepare($buy);
            $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
            $stmt->bindValue(':buydatetime',date("Y-m-d H:i:s"),PDO::PARAM_STR);
            $stmt->execute();
            //↓buydetailテーブルに挿入用ののbuy_idを取得する
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $buy_id=$row['buy_id'];
            //↓購入された商品を出品者の受注テーブルに送る
            $orders="insert into orders (ordersDatetime,endFlag,member_id,goodsCode) OUTPUT INSERTED.order_id values (getdate(),0,:member_id,:goodsCode)";
            $stmt=$dbh->prepare($orders);
            $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
            $stmt->bindValue(':goodsCode',$goodsCode,PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $order_id=$row['order_id'];
            //↓buydetailテーブルに挿入
            $buyDetail="insert into buydetail (buy_id,goodsCode,buyCount,price,request,order_id) Values (:buy_id,:goodsCode,:buyCount,:price,:request,:order_id)";
            $stmt=$dbh->prepare($buyDetail);
            $stmt->bindValue(':buy_id',$buy_id,PDO::PARAM_INT);
            $stmt->bindValue(':buyCount',$cartCount,PDO::PARAM_INT);
            $stmt->bindValue(':request',$request,PDO::PARAM_STR);
            $stmt->bindValue(':goodsCode',$goodsCode,PDO::PARAM_INT);
            $stmt->bindValue(':price',$price,PDO::PARAM_INT);
            $stmt->bindvalue(':order_id',$order_id,PDO::PARAM_INT);
            $stmt->execute();
            //↓cartテーブルを削除
            $cart="DELETE FROM cart WHERE member_id=:member_id";
            $stmt=$dbh->prepare($cart);
            $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
            $stmt->execute();
            //goodsテーブルの在庫を減らす、もし０になったら出品フラグを１に変更
            $goods="select stock from goods where goodscode=:goodsCode";
            $stmt=$dbh->prepare($goods);
            $stmt->bindValue(':goodsCode',$goodsCode,PDO::PARAM_INT);
            $stmt->execute();
            $stock=$stmt->fetch(PDO::FETCH_ASSOC);
            if ($stock['stock']==$cartCount){
                $goodsStock="update goods set stock=0,sellFlag=0 where goodsCode=:goodsCode";
                $stmt=$dbh->prepare($goodsStock);
                $stmt->bindValue(':goodsCode',$goodsCode,PDO::PARAM_INT);
                $stmt->execute();   
            }else{
                $goodsStock="update goods set stock=stock- :buyCount where goodsCode=:goodsCode";
                $stmt=$dbh->prepare($goodsStock);
                $stmt->bindValue(':buyCount',$cartCount,PDO::PARAM_INT);
                $stmt->bindValue(':goodsCode',$goodsCode,PDO::PARAM_INT);
                $stmt->execute();
            }

        }
    }
?>