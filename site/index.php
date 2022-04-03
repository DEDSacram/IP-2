<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BasePage {
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
        return $this->m->render("crossroad");
        
        
    }
}

(new CurrentPage())->render();
