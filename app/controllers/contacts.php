<?php



class contacts extends Controller
{
    public function index()
    {
        $this->view("user", "main", [], false);
    }

    public function add()
    {
        
    }
    public function edit()
    {
        echo 'contactsedit methodu çalıştı';
    }
}
