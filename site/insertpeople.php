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
        $this->title = "Nový člověk";
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

            $this->employee = PeopleModel::readPostData();
          
            //validovat
            $isOk = $this->employee->validate();
            
            //když jsou validní
            if ($isOk) {
                //uložit
                if ($this->employee->insert()) {
                    //přesměruj, ohlas úspěch
                    $this->state = self::STATE_FORM_REQUESTED;
                    echo "OK";
                } else {
                    //přesměruj, ohlas chybu
                    $this->state = self::STATE_FORM_REQUESTED;
                    echo "Nezdařilo se";
                }
            } else {
                $this->state = self::STATE_FORM_REQUESTED;
            }
        } else {
            $this->state = self::STATE_FORM_REQUESTED;
            $this->employee = new PeopleModel();
        }

    }


    protected function body(): string
    {
        require "../includes/adminredirect.inc.php";
        if ($this->state == self::STATE_FORM_REQUESTED)
            return $this->m->render(
                "newPeople",
                [
                    'room' => $this->employee,
                    'errors' => $this->employee->getValidationErrors(),
                    'action' => "create"
                ]
            );
        elseif ($this->state == self::STATE_PROCESSED){
            //vypiš výsledek zpracování
            if ($this->result == self::RESULT_SUCCESS) {
                echo "Uspesne";
                return "";
            } else {
                echo "Neuspesne";
               return "";            
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
        if ($action == "create"){
            return self::STATE_FORM_SENT;
        }
        //jinak chci form
        return self::STATE_FORM_REQUESTED;
    }

   

}

(new CurrentPage())->render();
