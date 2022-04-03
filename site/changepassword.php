<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
    public function __construct()
    {
        parent::__construct();
        $this->title = "Password Reset";
    }
    public function Change($newpassword,$confirmpassword,$user){
   
        if($newpassword == $confirmpassword){
    
            $query = "UPDATE employee SET password=:newpassword WHERE login=:user";
            $stmt = DB::getConnection()->prepare($query);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':newpassword', password_hash($newpassword,PASSWORD_DEFAULT));

          
         
            
            $stmt->execute();
        }
        
    }
   
    protected function body(): string
    {
        require "../includes/redirect.inc.php";
        if($_POST){
            $this->Change(filter_input(INPUT_POST, $_POST['password']),filter_input(INPUT_POST,$_POST['confirmpassword']),filter_input(INPUT_POST,$_SESSION['user']));
        }
            return $this->m->render(
                "changepassword",
                [
                    'errors' => $this->getValidationErrors(),
                ]
            );
    }
}

(new CurrentPage())->render();
