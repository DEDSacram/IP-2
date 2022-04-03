<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage {
    const STATE_FORM_REQUESTED = 1;
    const STATE_FORM_SENT = 2;
    const STATE_PROCESSED = 3;

    const RESULT_SUCCESS = 1;
    const RESULT_FAIL = 2;

    private int $state;
    private int $result = 0;




    private Login $LoginData;
    private string $loginerror = "";
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
    public function __construct()
    {
        parent::__construct();
        $this->title = "Login";
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->state = $this->getState();

        if ($this->state == self::STATE_PROCESSED) {
            //reportuju

        } elseif ($this->state == self::STATE_FORM_SENT) {
            //přišla data
            //načíst

            $this->LoginData = Login::readPostData();
            //validovat
            $isOk = $this->LoginData->validate();
            
            //když jsou validní
            if ($isOk) {
                //uložit
              
                if ($this->LoginData->Verify()) {
                    //přesměruj, ohlas úspěch
                    session_start();
                    $_SESSION["start"] = true;
                    $_SESSION["user"] = $this->LoginData->login;
                    $_SESSION["admin"] = $this->LoginData->admin;
                  
                    $this->redirect(self::RESULT_SUCCESS);
                } else {
                    $this->state = self::STATE_FORM_REQUESTED;
                    $this->loginerror = "Špatné příhlášení";
                 
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
            }
        } else {
            $this->state = self::STATE_FORM_REQUESTED;
            $this->LoginData = new Login();
        }

    }
   
    protected function body(): string
    {
            if ($this->state == self::STATE_FORM_REQUESTED){
            $validerros = $this->LoginData->getValidationErrors();
            $validerros['badlogin'] = $this->loginerror;
                return $this->m->render(
                    "loginForm",
                    [
                        'errors' => $validerros,
                        'action' => "Login"
                    ]
                );
            }
            elseif ($this->state == self::STATE_PROCESSED){
                //vypiš výsledek zpracování
                if ($this->result == self::RESULT_SUCCESS) {
                    $host  = $_SERVER['HTTP_HOST'];
                    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                    header("Location: http://$host$uri");
                  
                }
            }
            return "";
    }

    protected function getState() : int
    {
 
        $result = filter_input(INPUT_GET, 'result', FILTER_VALIDATE_INT);

        if ($result == self::RESULT_SUCCESS) {
            $this->result = self::RESULT_SUCCESS;
            return self::STATE_PROCESSED;
        } elseif($result == self::RESULT_FAIL) {
            $this->result = self::RESULT_FAIL;
            return self::STATE_PROCESSED;
        }

       
        $action = filter_input(INPUT_POST, 'action');
        if ($action == "Login"){
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
