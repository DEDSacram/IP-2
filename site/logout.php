<?php
require "../includes/bootstrap.inc.php";

final class CurrentPage extends BasePage {
    protected string $title = "Logout";

    protected function body(): string
    {
        require "../includes/redirect.inc.php";
        session_destroy();
        return $this->m->render("logout");
        
        
    }
}

(new CurrentPage())->render();
