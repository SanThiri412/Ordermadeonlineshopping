<?php
//森下
require_once "DAO.php";

class Member{
    public ?string $member_id=null;
    public string $name;
    public string $kanaName;
    public string $nickName;
    public string $email;
    public string $tel;
    public string $postcode;
    public string $address;
    public string $password;
    public string $cardnumber;
    public ?string $member_image;
    public ?string $self_introduction;
}
class member_get_image_number{
    public string $member_image;
}
//森下
class MemberDAO
{
    public function get_member(string $email,string $password){
        $dbh=DAO::get_db_connect();
        $sql="SELECT * FROM member WHERE email=:email";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
        $member=$stmt->fetchObject('Member');
        //サンティリヤダナマウン
        if($member !==false){
           if(password_verify($password,$member->password)){
              return $member;
           }
                
        }
        return false;
    }

    public function get_images(string $email){
        $dbh=DAO::get_db_connect();

        $sql="SELECT * FROM member WHERE email=:email";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->fetchObject()!==false){
            return true;
        }else{
            return false;
        }
    }
    public function images_number(){
        $dbh=DAO::get_db_connect();
        $sql="SELECT TOP 1 member_image FROM member ORDER BY member_id DESC;";
        $stmt=$dbh->prepare($sql);
        $stmt->execute();
        $result =$stmt-> fetchobject("member_get_image_number");
        if ($result === false) {
            return 0;
         }
            return (int)$result->member_image;

    }
    //森下
    public function insert(Member $member){
        $dbh=DAO::get_db_connect();

        $sql="INSERT into member(name,kanaName,nickName,email,tel,postcode,address,password,cardnumber,member_image,self_introduction) VALUES (:name,:kanaName,:nickName,:email,:tel,:postcode,:address,:password,:cardnumber,:member_image,:self_introduction)";

        $stmt=$dbh->prepare($sql);
        $password=password_hash($member->password,PASSWORD_DEFAULT);
        $stmt->bindValue(':name',$member->name,PDO::PARAM_STR);
        $stmt->bindValue(':kanaName',$member->kanaName,PDO::PARAM_STR);
        $stmt->bindValue(':nickName',$member->nickName,PDO::PARAM_STR);
        $stmt->bindValue(':email',$member->email,PDO::PARAM_STR);
        $stmt->bindValue(':tel',$member->tel,PDO::PARAM_STR);
        $stmt->bindValue(':postcode',$member->postcode,PDO::PARAM_STR);
        $stmt->bindValue(':address',$member->address,PDO::PARAM_STR);
        $stmt->bindValue(':password',$password,PDO::PARAM_STR);
        $stmt->bindValue(':cardnumber',$member->cardnumber,PDO::PARAM_STR);
        $stmt->bindValue(':member_image',$member->member_image,PDO::PARAM_STR);
        $stmt->bindValue(':self_introduction',$member->self_introduction,PDO::PARAM_STR);
        $stmt->execute();
    }
    //内田
    public function update(Member $member){
        $dbh=DAO::get_db_connect();

        $sql="update member set name=:name,kanaName=:kanaName,nickName=:nickName,email=:email,tel=:tel,postcode=:postcode,address=:address,password=:password,cardnumber=:cardnumber,self_introduction=:self_introduction where member_id=:member_id";

        $stmt=$dbh->prepare($sql);
        $password=password_hash($member->password,PASSWORD_DEFAULT);
        $stmt->bindValue(':member_id',$member->member_id,PDO::PARAM_STR);
        $stmt->bindValue(':name',$member->name,PDO::PARAM_STR);
        $stmt->bindValue(':kanaName',$member->kanaName,PDO::PARAM_STR);
        $stmt->bindValue(':nickName',$member->nickName,PDO::PARAM_STR);
        $stmt->bindValue(':email',$member->email,PDO::PARAM_STR);
        $stmt->bindValue(':tel',$member->tel,PDO::PARAM_STR);
        $stmt->bindValue(':postcode',$member->postcode,PDO::PARAM_STR);
        $stmt->bindValue(':address',$member->address,PDO::PARAM_STR);
        $stmt->bindValue(':password',$password,PDO::PARAM_STR);
        $stmt->bindValue(':cardnumber',$member->cardnumber,PDO::PARAM_STR);
        $stmt->bindValue(':self_introduction',$member->self_introduction,PDO::PARAM_STR);
        $stmt->execute();
    }

    //森下
    public function email_exists(string $email){
        $dbh=DAO::get_db_connect();

        $sql="SELECT * FROM member WHERE email=:email";
        $stmt=$dbh->prepare($sql);
        $stmt->bindValue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->fetchObject()!==false){
            return true;
        }else{
            return false;
        }
    }

    //アウン
     public function findByName(string $name, bool $exact = false): array {
        $dbh = DAO::get_db_connect();
        if ($exact) {
            $sql = "SELECT * FROM member WHERE name = :name_n OR nickName = :name_nn";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name_n',  $name, PDO::PARAM_STR);
            $stmt->bindValue(':name_nn', $name, PDO::PARAM_STR);
        } else {
            $sql = "SELECT * FROM member WHERE name LIKE :name_n OR nickName LIKE :name_nn";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':name_n',  '%' . $name . '%', PDO::PARAM_STR);
            $stmt->bindValue(':name_nn', '%' . $name . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        $members = [];
        while ($member = $stmt->fetchObject('Member')) {
            $members[] = $member;
        }
        return $members;
    }
    //サンティリヤダナマウン
    public function get_member_by_id(int $member_id): ?Member {
        $dbh = DAO::get_db_connect();
        $sql = "SELECT * FROM member WHERE member_id = :member_id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $stmt->execute();
        $member = $stmt->fetchObject('Member');
        return $member !== false ? $member : null;
    }
<<<<<<< HEAD
}
=======

}
?>
>>>>>>> 562e5a8566790922276e16ed9e5c86cbb51a7c01
