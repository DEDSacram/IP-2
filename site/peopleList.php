<?php
require "../includes/bootstrap.inc.php";
//WORKS
final class CurrentPage extends BaseDBPage {
    protected string $title = "List Zamestnancu";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
       
        switch(filter_input(INPUT_GET,"poradi")){
            case "nazev_up":
                $query = "SELECT employee_id,employee.name,surname,job,room.name as RoomName,phone FROM employee INNER JOIN room ON employee.room = room_id ORDER BY surname DESC";
                break;
            case "mistnost_up":
                $query = "SELECT employee_id,employee.name,surname,job,room.name as RoomName,phone FROM employee INNER JOIN room ON employee.room = room_id ORDER BY room.name DESC";
                break;
            case "telefon_up":
                $query = "SELECT employee_id,employee.name,surname,job,room.name as RoomName,phone FROM employee INNER JOIN room ON employee.room = room_id ORDER BY phone DESC";
                break;
            case "pozice_up":
                $query ="SELECT employee_id,employee.name,surname,job,room.name as RoomName,phone FROM employee INNER JOIN room ON employee.room = room_id ORDER BY job DESC";
                break;
            default:
                $query = "SELECT employee_id,employee.name,surname,job,room.name as RoomName,phone FROM employee INNER JOIN room ON employee.room = room_id";
            }
        if($_SESSION["admin"]){
           $remove = filter_input(INPUT_GET,"employee_id");
           if($remove){
            PeopleModel::deleteById($remove);
           }
     
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([]);
        return  $this->m->render("peopleList",["data" => $stmt,"admin"=>$_SESSION["admin"]]);
    }
}

(new CurrentPage())->render();
