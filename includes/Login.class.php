<?php
final class Login
{
    
    public string $login;
    public string $password;
    public string $admin;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $roomData = [])
    {
        $this->login = $roomData['login'] ?? "";
        $this->password = $roomData['password'] ?? "";
    }

    public function validate() : bool
    {
        $isOk = true;
        if ($this->login == "") {
            $isOk = false;
            $this->validationErrors['login'] = "Login je povinný";
        }
        if ($this->password == "") {
            $isOk = false;
            $this->validationErrors['password'] = "Password je povinný";
        }
        return $isOk;
    }
    public function Verify() : bool
    {
        echo password_hash('franti',PASSWORD_DEFAULT);
        $query = "SELECT login,password,admin FROM employee WHERE login=:login";
        
        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':login', $this->login);
        $stmt->execute();
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $dbData['password'])){
                $this->admin = $dbData['admin'];
                return true;
            }else{
                return false;
            }
        
     
       
     
    }

    public static function readPostData() : Login
    {
        return new self($_POST);            //není úplně košer, nefiltruju
    }
}