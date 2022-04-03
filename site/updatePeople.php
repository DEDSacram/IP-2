<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private PeopleModel $employee;
    private int $result = 0;


    //když nepřišla data a není hlášení o výsledku, chci zobrazit formulář
    //když přišla data
      //validuj
      //když jsou validní
        //ulož a přesměruj zpět (PRG)
        //jinak vrať do formuláře
    public function __construct()
    {
        parent::__construct();
        $this->title = "Edit Člověk";
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->state = $this->getState();
  
  
            $this->employee = PeopleModel::readPostData();


          
 
            //validovat
            $isOk = $this->employee->validate();

            //když jsou validní
            if ($isOk) {
                $query = "SELECT key_id FROM `key` INNER JOIN room ON room = room_id WHERE employee=:employeeid";
                $stmt = DB::getConnection()->prepare($query);
                $stmt->bindParam(':employeeid', $this->employee->employee_id);
                $stmt->execute();
                $keys = $stmt->fetchAll();

       
            

       

                $countkeys = count($keys);
                foreach($_POST as $post=>$postvalue){
                    $change = substr($post, 0,3);
                    $value = substr($post,3);
            
                    if($change == "rok"){
                
                     $query = "INSERT INTO `key` (employee, room)
                     VALUES (:employeeid,:roomid)";
                      $stmt = DB::getConnection()->prepare($query);
                      $stmt->bindParam(':employeeid', $this->employee->employee_id);
                      $stmt->bindParam(':roomid', $value);
                      $stmt->execute();
                    }
                    elseif ($change == "key"){
                        for ($i = 0; $i <= $countkeys; $i++) {
                            if($keys[$i]->key_id == $value){
                              unset($keys[$i]);
                              break;
                            }
                        }
                        
                       
                         
                    }
                }
                foreach($keys as $key){
                    $query = "DELETE FROM `key` WHERE key_id=:key_id";
                    $stmt = DB::getConnection()->prepare($query);
                    $stmt->bindParam(':key_id', $key->key_id);
                    $stmt->execute();
                }
     
     



                if ($this->employee->update()) {
                    //přesměruj, ohlas úspěch
                    $this->state = self::STATE_FORM_REQUESTED;
                    echo "Uloženo";
                } else {
                    //přesměruj, ohlas chybu
                    $this->state = self::STATE_FORM_REQUESTED;
                    echo "Něco se pokazilo";
                }
            } 
     
         
            $employeeId = filter_input(INPUT_GET, "employee_id");
            $this->employee = PeopleModel::findByIdwithoutpass($employeeId);
     

    }


    protected function body(): string
    {
        require "../includes/adminredirect.inc.php";
       
    
            $query = "SELECT room_id,key_id FROM `key` INNER JOIN room ON room = room_id WHERE employee=:employeeid";
            $stmt = DB::getConnection()->prepare($query);
            $stmt->bindParam(':employeeid', $this->employee->employee_id);
            $stmt->execute();
            $keys = $stmt->fetchAll();

       
            $this->keys = $keys;
            $query = "SELECT room_id,'0' AS `show`,name,'' AS key_id FROM room";
            $stmt = DB::getConnection()->prepare($query);
            $stmt->execute();
            $employees = $stmt->fetchAll();
            foreach($keys as $key){
                foreach($employees as $employee){
                    if($key->room_id == $employee->room_id){
               
                        $employee->show = 1;
                        $employee->key_id = $key->key_id;
                    }
                }
            }

            $query = "SELECT `admin` FROM employee WHERE employee_id=:employeeid";
            $stmt = DB::getConnection()->prepare($query);
            $stmt->bindParam(':employeeid', $this->employee->employee_id);
            $stmt->execute();
            $admin = $stmt->fetch();
        
    
            return $this->m->render(
                "newPeople",
                [
                    'room' => $this->employee,
                    'admin' => $admin->admin,
                    'keys' => $employees,
                    'errors' => $this->employee->getValidationErrors(),
                    'action' => "update"
                ]
            );
  
        if ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                echo "Sucess";
            } else {
                echo "Failed";
            }
        }
        return "";
    }

    protected function getState() : int
    {
        //když mám result -> zpracováno
        $result = filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);

        if ($result == self::RESULT_SUCCESS) {
            $this->result = self::RESULT_SUCCESS;
            return self::STATE_PROCESSED;
        } elseif($result == self::RESULT_FAIL) {
            $this->result = self::RESULT_FAIL;
            return self::STATE_PROCESSED;
        }

        //nebo když mám post -> zvaliduju a buď uložím nebo form
        $action = filter_input(INPUT_POST, 'action');
        if ($action == "update"){
            return self::STATE_FORM_SENT;
        }
        //jinak chci form
        return self::STATE_FORM_REQUESTED;
    }

    private function redirect(int $result) : void {
        $location = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: {$location}?result={$result}");
        exit;
    }

}

(new CurrentPage())->render();
