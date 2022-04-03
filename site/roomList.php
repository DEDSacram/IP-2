<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
        switch(filter_input(INPUT_GET,"poradi")){
        case "nazev_up":
            $query ="SELECT room_id,room.name,phone,room.no FROM room ORDER BY room.name DESC";
            break;
        case "cislo_up":
            $query = "SELECT room_id,room.name,phone,room.no FROM room ORDER BY room.no DESC";
            break;
        case "telefon_up":
            $query = "SELECT room_id,room.name,phone,room.no FROM room ORDER BY room.phone DESC";
            break;
        default:
            $query = "SELECT room_id,room.name,phone,room.no FROM room";
            break;
         
        }
        if($_SESSION["admin"]){
            $remove = filter_input(INPUT_GET,"room_id");
            if($remove){
             RoomModel::deleteById($remove);
            }
        }
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([]);
        return $this->m->render("roomList", ["rooms" => $stmt,"admin"=>$_SESSION["admin"]]);
    }
}

(new CurrentPage())->render();
