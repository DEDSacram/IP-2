<?php
require "../includes/bootstrap.inc.php";
//WORKS
final class CurrentPage extends BaseDBPage {
    protected string $title = "Test modelu";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
       $roomid = (filter_input(INPUT_GET,"room_id"));

    $stmt = DB::getConnection()->prepare("SELECT room.name,phone,room.no FROM room WHERE room_id = :roomid");
    $stmt->bindParam(':roomid', $roomid);
    $stmt->execute();
    $room = $stmt->fetch();

    $stmtplat = DB::getConnection()->prepare("SELECT ROUND(AVG(wage),2) as mage FROM employee WHERE employee.room = :roomid");
    $stmtplat->bindParam(':roomid', $roomid);
    $stmtplat->execute();
    $plat = $stmtplat->fetch();

    $stmtosoba = DB::getConnection()->prepare("SELECT employee.wage,employee.surname,CONCAT(LEFT(employee.name, 1),'.') as nameshort,employee_id FROM employee WHERE employee.room = :roomid");
    $stmtosoba->bindParam(':roomid', $roomid);
    $stmtosoba->execute();
    $osoby = $stmtosoba->fetchAll();   
    
    $stmtkeys = DB::getConnection()->prepare("SELECT employee_id,surname,CONCAT(LEFT(name, 1),'.') as nameshort FROM `key` as c INNER JOIN employee ON c.employee = employee_id WHERE c.room = :roomid");
    $stmtkeys->bindParam(':roomid', $roomid);
    $stmtkeys->execute();
    $klice = $stmtkeys->fetchAll();
  
     
        
       
        return  $this->m->render("room", ["room" => $room,"plat"=>$plat,"osoby"=>$osoby,"keys"=>$klice]);
    }
}

(new CurrentPage())->render();
