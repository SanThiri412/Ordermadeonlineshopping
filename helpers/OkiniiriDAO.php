<?php
//森下
 require_once 'DAO.php' ;

 Class Favorite_goods
 {
    public int $member_id;
    public int $favorite_goodsCode;
    public string $goodsName;
    public string $goods_image;
 }
 Class Favorite_member
 {
    public int $member_id;
    public int $favorite_member_id;
    public string $nickName;
    public string $member_image;
 }
 Class okiniiriDAO
 {
    public function get_goods_Okiniiri_by_memberid(int $member_id)
    {
        $dbh=DAO::get_db_connect();
        $sql="SELECT Favorite_goods.member_id,Favorite_goods.favorite_goodsCode,Goods.goodsName,Goods.goods_image FROM Favorite_goods join Goods on Favorite_goods.favorite_goodsCode=Goods.goodscode WHERE Favorite_goods.member_id=:member_id";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
        $data=[];
        while($row = $stmt-> fetchobject("Favorite_goods")){
            $data[]=$row;
        }
        return $data;
    }
     public function get_member_Okiniiri_by_memberid(int $member_id)
    {
        $dbh=DAO::get_db_connect();
        $sql="SELECT Favorite_member.member_id,Favorite_member.favorite_member_id,Member.nickName,Member.member_image FROM Favorite_member join Member on Favorite_member.favorite_member_id=Member.member_id WHERE Favorite_member.member_id=:member_id";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
        $data=[];
        while($row = $stmt-> fetchobject("Favorite_member")){
            $data[]=$row;
        }
        return $data;
    }
     public function goods_delete(int $member_id,int $favorite_goodsCode){
        $dbh=DAO::get_db_connect();

        $sql="DELETE FROM Favorite_goods WHERE favorite_goodsCode=:favorite_goodsCode AND member_id=:member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':favorite_goodsCode',$favorite_goodsCode,PDO::PARAM_INT);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
    }
    public function member_delete(int $favorite_member_id,int $member_id){
        $dbh=DAO::get_db_connect();

        $sql="DELETE FROM Favorite_member WHERE favorite_member_id=:favorite_member_id AND  member_id=:member_id";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':favorite_member_id',$favorite_member_id,PDO::PARAM_INT);
        $stmt->bindValue(':member_id',$member_id,PDO::PARAM_INT);
        $stmt->execute();
    }
 }
  //森下
 ?>
 