<?php
//森下
require_once 'DAO.php' ;
class History//PTYA
{
    public int $History_member_id;
    public string $goodscode;
    public string $goodsText;
    public string $goodsname;
    public int $goods_member_id;
    public string $goods_image;
    public string $nickName;
    public string $ordersDatetime;
}

 Class buy
 {
    public int $buy_member_id;
    public string $goodscode;
    public string $goodsname;
    public string $goods_image;
    public string $nickName;
    public int $goods_member_id; 
    public string $buyDatetime;
    public string $goodsText;
 }
 Class Orders{
    public int $Orders_member_id;
    public int $goodscode;
    public string $goodsname;
    public int $member_id;
    public string $goods_image;
    public string $ordersDatetime;
    public string $nickName;
    public int $endFlag;
    public string $goodsText;
    public ?string $request;
 }
 Class HistoryDAO
 {
    public function get_History_by_memberid(int $member_id)
    {
        $dbh=DAO::get_db_connect();
        $sql="SELECT History.member_id AS History_member_id,Goods.goodscode,Goods.goodsText,Goods.goodsname,Goods.member_id AS goods_member_id ,Goods.goods_image,Member.nickName,Orders.ordersDatetime FROM Goods join History on Goods.goodscode=History.goodscode join Member on Goods.member_id=Member.member_id join Orders on Orders.goodscode=Goods.goodscode WHERE History.member_id=:member_id";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
        $data=[];
        while($row = $stmt-> fetchobject("History")){
            $data[]=$row;
        }
        return $data;
    }
    
    public function get_buyOrders_by_memberid(int $member_id)
    {
        $dbh=DAO::get_db_connect();
        $sql="SELECT Orders.member_id AS Orders_member_id,
            Goods.goodscode,Goods.goodsText,
            Goods.goodsname,
            Goods.member_id,
            Goods.goods_image,
            Orders.ordersDatetime,
            Member.nickName,
            Orders.endFlag,
            BuyDetail.request
            FROM Goods 
            JOIN Orders ON Orders.goodsCode = Goods.goodsCode 
            JOIN Member ON Orders.member_id = Member.member_id
            JOIN BuyDetail ON Orders.order_id = BuyDetail.order_id
            WHERE Goods.member_id=:member_id";
            $stmt=$dbh->prepare($sql);
            $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
            $stmt->execute();

        $data=[];
        while($row = $stmt-> fetchobject("Orders")){
            $data[]=$row;
        }
        return $data;
    }

    public function goods_go(int $member_id,int $goodscode){
        $dbh=DAO::get_db_connect();
        $sql="UPDATE Orders SET endFlag=1 WHERE member_id=:member_id AND goodsCode=:goodsCode" ;
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->bindValue(':goodsCode',$goodscode,PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public function goods_no(int $member_id,int $goodscode){
        $dbh=DAO::get_db_connect();
        $sql="UPDATE Orders SET endFlag=0 WHERE member_id=:member_id AND goodsCode=:goodsCode" ;
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->bindValue(':goodsCode',$goodscode,PDO::PARAM_INT);
        $stmt->execute();
    }


    public function get_buy_by_memberid(int $member_id)
    {
        $dbh=DAO::get_db_connect();
        $sql="SELECT
            Buy.member_id AS buy_member_id,
            BuyDetail.goodscode,
            Goods.goodsText,
            Goods.goodsname,
            Goods.member_id AS goods_member_id,
            Goods.goods_image,
            Member.nickName,
            buy.buyDatetime

            FROM Buy
            JOIN BuyDetail ON buy.buy_id=BuyDetail.buy_id
            JOIN Goods ON Goods.goodscode = BuyDetail.goodscode
            JOIN Member ON Member.member_id = Goods.member_id
			
            WHERE Buy.member_id =:member_id";

        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
        $data=[];
        while($row = $stmt-> fetchobject("buy")){
            $data[]=$row;
        }
        return $data;
    }
   
}
//森下
?>