<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
   
        $clovekId = filter_input(INPUT_GET,'personid');
        if($clovekId == null){
          header('HTTP/1.0 404 Not Found');
          echo "<h1>404 Not Found</h1>";
          echo "Stránka nenalezena.";
          exit();
        }
        $stmt =  DB::getConnection()->prepare("SELECT room_id,employee.name as name,surname,CONCAT(LEFT(surname, 1),'.') as surnameshort,job,wage,room.name as RoomName FROM employee INNER JOIN room ON employee.room = room_id WHERE employee_id = :clovekid");
        $stmt->bindParam(':clovekid', $clovekId);
        $stmt->execute();
        $Dbdata = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmtkeys = DB::getConnection()->prepare("SELECT room_id,room.name as RoomName FROM `key` as c INNER JOIN room ON c.room = room_id WHERE employee = :clovekid");
        $stmtkeys->bindParam(':clovekid', $clovekId);
        $stmtkeys->execute();
        $datakeys = $stmtkeys->fetchAll();
        return $this->m->render("persontab",["data" => $Dbdata,"keys" => $datakeys]);
    }
}

(new CurrentPage())->render();
