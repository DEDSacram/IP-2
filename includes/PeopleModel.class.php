<?php

final class PeopleModel
{
    public ?int $employee_id;
    public string $name;
    public string $surname;
    public string $job;
    public string $wage;
    public string $room;
    public string $login;
    public string $password;
    public ?string $admin;

    private array $validationErrors = [];
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function __construct(array $EmployeeData = [])
    {
        $id = $EmployeeData['employee_id'] ?? null;
        if (is_string($id))
            $id = filter_var($id, FILTER_VALIDATE_INT);

        $this->employee_id = $id;
        $this->name = $EmployeeData['name'] ?? "";
        $this->surname = $EmployeeData['surname'] ?? "";
        $this->job = $EmployeeData['job'] ?? "";
        $this->wage = $EmployeeData['wage'] ?? 0;
        $this->room = $EmployeeData['room'] ?? 0;
        $this->login = $EmployeeData['login'] ?? "";
        $this->password = $EmployeeData['password'] ?? "";
        $this->admin = $EmployeeData['admin'] ?1:0;
    }

    public function validate() : bool
    {
        $isOk = true;

        if (!$this->name) {
            $isOk = false;
            $this->validationErrors['name'] = "Jméno je povinné";
        }
        if (!$this->surname) {
            $isOk = false;
            $this->validationErrors['surname'] = "Příjmení je povinné";
        }
        if (!$this->job) {
            $isOk = false;
            $this->validationErrors['job'] = "Pole práce je povinné";
        }
        if (!$this->wage) {
            $isOk = false;
            $this->validationErrors['wage'] = "Pole plat je povinné";
        }
        if (!$this->room) {
            $isOk = false;
            $this->validationErrors['room'] = "Pole místnost je povinné";
        }
        if (!$this->login) {
            $isOk = false;
            $this->validationErrors['login'] = "Pole login je povinné";
        }
     

        return $isOk;
    }

    public function insert() : bool
    {
        $query = "INSERT INTO employee (name, surname, job,wage,room,login,password,admin) VALUES (:name, :surname, :job,:wage,:room,:login,:password,:admin)";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':password',password_hash($this->password, PASSWORD_DEFAULT));
        $stmt->bindParam(':admin', $this->admin);

        if (!$stmt->execute())
            return false;

        $this->employee_id = DB::getConnection()->lastInsertId();
        return true;
    }

    public function update() : bool
    {
        if($this->password == ''){
            $query = "UPDATE employee SET name=:name, surname=:surname,job=:job,wage=:wage,room=:room,login=:login,admin=:admin WHERE employee_id=:roomId";
            $stmt = DB::getConnection()->prepare($query);
        }
        else{
        $query = "UPDATE employee SET name=:name, surname=:surname,job=:job,wage=:wage,room=:room,login=:login,password=:password,admin=:admin WHERE employee_id=:roomId";
        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':password',password_hash($this->password, PASSWORD_DEFAULT));
        }
      

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $this->employee_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':surname', $this->surname);
        $stmt->bindParam(':job', $this->job);
        $stmt->bindParam(':wage', $this->wage);
        $stmt->bindParam(':room', $this->room);
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':admin', $this->admin);

        return $stmt->execute();
    }

    public function delete() : bool
    {
        return self::deleteById($this->employee_id);
    }

    public static function deleteById(int $employee_id) : bool {

        $query = "DELETE FROM employee WHERE employee_id=:roomId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $employee_id);

        return $stmt->execute();
    }

    public static function findById(int $employee_id) : ?PeopleModel
    {
        $query = "SELECT * FROM employee WHERE employee_id=:roomId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $employee_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbData)
            return null;

        return new self($dbData);
    }

    public static function findByIdwithoutpass(int $employee_id) : ?PeopleModel
    {
        $query = "SELECT employee_id,name,surname,job,wage,room,login,admin FROM employee WHERE employee_id=:roomId";

        $stmt = DB::getConnection()->prepare($query);
        $stmt->bindParam(':roomId', $employee_id);

        $stmt->execute();

        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dbData)
            return null;

        return new self($dbData);
    }
 
    public static function readPostData() : PeopleModel
    {
        return new self($_POST); //není úplně košer, nefiltruju
    }
}